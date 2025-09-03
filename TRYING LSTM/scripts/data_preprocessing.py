"""
Improved Data Preprocessing for LSTM Inventory Forecasting
User: Alysoffar
Current Date: 2025-09-03 19:02:25 UTC

This is a COMPLETE REPLACEMENT for the old data_preprocessing.py
"""

import pandas as pd
import numpy as np
from sklearn.preprocessing import RobustScaler, LabelEncoder
import matplotlib.pyplot as plt
import seaborn as sns
from datetime import datetime
import warnings
warnings.filterwarnings('ignore')

class ImprovedDataPreprocessor:
    def __init__(self):
        self.encoders = {}
        self.feature_columns = []
        self.target_column = 'Units Sold'
        
    def load_and_clean_data(self, file_path):
        """
        Load data with improved cleaning and validation
        """
        print(f"📊 Loading data from {file_path}...")
        
        # Load data
        df = pd.read_csv(file_path)
        original_shape = df.shape
        
        # Convert Date to datetime
        df['Date'] = pd.to_datetime(df['Date'], errors='coerce')
        
        # Remove rows with invalid dates
        df = df.dropna(subset=['Date'])
        
        # Sort by Store, Product, and Date
        df = df.sort_values(['Store ID', 'Product ID', 'Date']).reset_index(drop=True)
        
        print(f"   • Original shape: {original_shape}")
        print(f"   • After date cleaning: {df.shape}")
        print(f"   • Date range: {df['Date'].min()} to {df['Date'].max()}")
        
        return df
    
    def validate_data_quality(self, df):
        """
        Validate data quality and remove problematic records
        """
        print("🔍 Validating data quality...")
        
        initial_count = len(df)
        
        # 1. Remove negative or zero sales, inventory, prices
        df = df[df['Units Sold'] >= 0]
        df = df[df['Inventory Level'] >= 0]
        df = df[df['Price'] > 0]
        df = df[df['Demand Forecast'] >= 0]
        
        print(f"   • After removing negative/zero values: {len(df)} records")
        
        # 2. Remove extreme outliers using IQR method
        def remove_outliers(series, factor=2.0):
            Q1 = series.quantile(0.25)
            Q3 = series.quantile(0.75)
            IQR = Q3 - Q1
            lower = Q1 - factor * IQR
            upper = Q3 + factor * IQR
            return (series >= max(lower, 0)) & (series <= upper)
        
        # Apply outlier removal to key numeric columns
        mask = remove_outliers(df['Units Sold'], 1.5)  # More aggressive for target
        mask &= remove_outliers(df['Price'], 2.0)
        mask &= remove_outliers(df['Inventory Level'], 2.0)
        
        df = df[mask]
        print(f"   • After outlier removal: {len(df)} records")
        
        # 3. Remove store-product combinations with insufficient data
        min_records_per_combo = 35  # Need at least 5 weeks of data
        combo_counts = df.groupby(['Store ID', 'Product ID']).size()
        valid_combos = combo_counts[combo_counts >= min_records_per_combo].index
        
        df = df.set_index(['Store ID', 'Product ID']).loc[valid_combos].reset_index()
        print(f"   • Valid store-product combinations: {len(valid_combos)}")
        print(f"   • After filtering combinations: {len(df)} records")
        
        # 4. Remove combinations with zero variance in sales
        combo_variance = df.groupby(['Store ID', 'Product ID'])['Units Sold'].std()
        high_variance_combos = combo_variance[combo_variance > 2.0].index  # Need some variation
        
        df = df.set_index(['Store ID', 'Product ID']).loc[high_variance_combos].reset_index()
        print(f"   • After variance filtering: {len(df)} records")
        
        if len(df) < 1000:
            raise ValueError(f"Insufficient data after quality filtering: {len(df)} records")
        
        return df
    
    def create_effective_features(self, df):
        """
        Create only the most effective features (reduced complexity)
        """
        print("⚙️ Creating effective features...")
        
        df = df.copy()
        
        # 1. Time-based features (essential)
        print("   • Creating time features...")
        df['DayOfWeek'] = df['Date'].dt.dayofweek
        df['Month'] = df['Date'].dt.month
        df['Quarter'] = df['Date'].dt.quarter
        df['IsWeekend'] = (df['DayOfWeek'] >= 5).astype(int)
        df['IsMonthEnd'] = (df['Date'].dt.day >= 25).astype(int)
        df['IsMonthStart'] = (df['Date'].dt.day <= 7).astype(int)
        
        # 2. Cyclical encoding for seasonal patterns
        df['Month_Sin'] = np.sin(2 * np.pi * df['Month'] / 12)
        df['Month_Cos'] = np.cos(2 * np.pi * df['Month'] / 12)
        df['DayOfWeek_Sin'] = np.sin(2 * np.pi * df['DayOfWeek'] / 7)
        df['DayOfWeek_Cos'] = np.cos(2 * np.pi * df['DayOfWeek'] / 7)
        
        # 3. Price and discount features
        print("   • Creating price features...")
        df['Has_Discount'] = (df['Discount'] > 0).astype(int)
        df['Discount_Rate'] = df['Discount'] / 100
        df['Effective_Price'] = df['Price'] * (1 - df['Discount_Rate'])
        df['Price_Change'] = df.groupby(['Store ID', 'Product ID'])['Price'].pct_change()
        df['Price_vs_Competitor'] = df['Price'] / (df['Competitor Pricing'] + 0.01)
        
        # 4. Essential lag features (only what's proven useful)
        print("   • Creating lag features...")
        for lag in [1, 7]:  # Yesterday and last week
            df[f'Sales_Lag_{lag}'] = df.groupby(['Store ID', 'Product ID'])['Units Sold'].shift(lag)
            df[f'Inventory_Lag_{lag}'] = df.groupby(['Store ID', 'Product ID'])['Inventory Level'].shift(lag)
        
        # 5. Rolling averages (short and medium term)
        print("   • Creating rolling averages...")
        for window in [3, 7, 14]:
            df[f'Sales_MA_{window}'] = df.groupby(['Store ID', 'Product ID'])['Units Sold'].transform(
                lambda x: x.rolling(window=window, min_periods=2).mean()
            )
        
        # 6. Inventory and demand features
        print("   • Creating inventory features...")
        df['Inventory_Sales_Ratio'] = df['Inventory Level'] / (df['Sales_MA_7'] + 1)
        df['Demand_Accuracy'] = 1 - np.abs(df['Demand Forecast'] - df['Units Sold']) / (df['Units Sold'] + 1)
        df['Stockout_Risk'] = (df['Sales_MA_7'] > df['Inventory Level']).astype(int)
        
        # 7. Target encoding for categorical variables (more stable than label encoding)
        print("   • Creating target encodings...")
        for cat_col in ['Store ID', 'Product ID', 'Category']:
            # Use global mean to avoid overfitting
            global_mean = df['Units Sold'].mean()
            category_means = df.groupby(cat_col)['Units Sold'].mean()
            
            # Smooth with global mean (Bayesian approach)
            category_counts = df.groupby(cat_col).size()
            smoothing_factor = 10  # Higher = more smoothing
            
            smoothed_means = (category_means * category_counts + global_mean * smoothing_factor) / (category_counts + smoothing_factor)
            df[f'{cat_col}_Target_Encoded'] = df[cat_col].map(smoothed_means)
        
        # 8. Fill missing values strategically
        print("   • Filling missing values...")
        
        # Forward fill then backward fill for time series
        df = df.sort_values(['Store ID', 'Product ID', 'Date'])
        df = df.groupby(['Store ID', 'Product ID']).apply(
            lambda group: group.fillna(method='ffill').fillna(method='bfill')
        ).reset_index(drop=True)
        
        # Fill any remaining NaN with column means
        numeric_columns = df.select_dtypes(include=[np.number]).columns
        df[numeric_columns] = df[numeric_columns].fillna(df[numeric_columns].mean())
        
        print(f"   • Created {len(df.columns)} total columns")
        
        return df
    
    def select_best_features(self, df):
        """
        Select the most predictive features using correlation and business logic
        """
        print("🎯 Selecting best features...")
        
        # Define potential feature groups
        time_features = [
            'DayOfWeek', 'Month', 'Quarter', 'IsWeekend', 'IsMonthEnd', 'IsMonthStart',
            'Month_Sin', 'Month_Cos', 'DayOfWeek_Sin', 'DayOfWeek_Cos'
        ]
        
        price_features = [
            'Price', 'Effective_Price', 'Has_Discount', 'Discount_Rate',
            'Price_Change', 'Price_vs_Competitor'
        ]
        
        lag_features = [
            'Sales_Lag_1', 'Sales_Lag_7', 'Inventory_Lag_1', 'Inventory_Lag_7'
        ]
        
        rolling_features = [
            'Sales_MA_3', 'Sales_MA_7', 'Sales_MA_14'
        ]
        
        inventory_features = [
            'Inventory Level', 'Demand Forecast', 'Inventory_Sales_Ratio',
            'Demand_Accuracy', 'Stockout_Risk'
        ]
        
        categorical_features = [
            'Store ID_Target_Encoded', 'Product ID_Target_Encoded', 'Category_Target_Encoded'
        ]
        
        external_features = [
            'Holiday/Promotion', 'Competitor Pricing'
        ]
        
        # Combine all potential features
        all_potential_features = (time_features + price_features + lag_features + 
                                rolling_features + inventory_features + 
                                categorical_features + external_features)
        
        # Filter to only include features that exist in the dataframe
        available_features = [f for f in all_potential_features if f in df.columns]
        
        # Calculate correlation with target
        correlations = {}
        for feature in available_features:
            if df[feature].dtype in ['int64', 'float64']:
                corr = df[feature].corr(df['Units Sold'])
                if not np.isnan(corr):
                    correlations[feature] = abs(corr)
        
        # Sort by correlation strength
        sorted_features = sorted(correlations.items(), key=lambda x: x[1], reverse=True)
        
        print("   • Top 10 most correlated features:")
        for i, (feature, corr) in enumerate(sorted_features[:10]):
            print(f"     {i+1}. {feature}: {corr:.3f}")
        
        # Select top features + essential business features
        top_corr_features = [f[0] for f in sorted_features[:15]]  # Top 15 by correlation
        
        essential_features = [  # Always include these for business logic
            'Inventory Level', 'Demand Forecast', 'Price', 'Holiday/Promotion'
        ]
        
        # Combine and deduplicate
        self.feature_columns = list(set(top_corr_features + 
                                      [f for f in essential_features if f in df.columns]))
        
        print(f"   • Selected {len(self.feature_columns)} features for training")
        
        return self.feature_columns
    
    def create_sequences_for_store_product(self, group, sequence_length):
        """
        Create sequences for a specific store-product combination with validation
        """
        if len(group) < sequence_length + 5:  # Need extra buffer
            return [], []
        
        # Sort by date and reset index
        group = group.sort_values('Date').reset_index(drop=True)
        
        # Validate data quality
        if group['Units Sold'].std() < 1.0:  # Skip products with very low variance
            return [], []
        
        sequences = []
        targets = []
        
        try:
            feature_data = group[self.feature_columns].values
            target_data = group[self.target_column].values
            
            for i in range(len(feature_data) - sequence_length):
                seq = feature_data[i:(i + sequence_length)]
                target = target_data[i + sequence_length]
                
                # Validate sequence
                if (not np.isnan(seq).any() and not np.isnan(target) and 
                    target > 0 and np.isfinite(seq).all()):
                    sequences.append(seq)
                    targets.append(target)
                    
        except KeyError as e:
            print(f"      Warning: Missing features for store-product combination: {e}")
            return [], []
        
        return sequences, targets
    
    def get_preprocessing_summary(self, df):
        """
        Generate preprocessing summary for validation
        """
        summary = {
            'total_records': len(df),
            'date_range': (df['Date'].min(), df['Date'].max()),
            'stores': df['Store ID'].nunique(),
            'products': df['Product ID'].nunique(), 
            'categories': df['Category'].nunique(),
            'avg_sales': df['Units Sold'].mean(),
            'sales_std': df['Units Sold'].std(),
            'selected_features': len(self.feature_columns),
            'feature_list': self.feature_columns
        }
        
        return summary

def diagnose_and_preprocess(file_path, sequence_length=21):
    """
    Complete preprocessing pipeline with diagnosis
    """
    print("🚀 IMPROVED DATA PREPROCESSING PIPELINE")
    print("=" * 60)
    print(f"👤 User: Alysoffar")
    print(f"📅 Started: 2025-09-03 19:02:25 UTC")
    print("=" * 60)
    
    try:
        preprocessor = ImprovedDataPreprocessor()
        
        # Step 1: Load and clean
        df = preprocessor.load_and_clean_data(file_path)
        
        # Step 2: Validate quality
        df_clean = preprocessor.validate_data_quality(df)
        
        # Step 3: Create features
        df_featured = preprocessor.create_effective_features(df_clean)
        
        # Step 4: Select best features
        feature_columns = preprocessor.select_best_features(df_featured)
        
        # Step 5: Generate summary
        summary = preprocessor.get_preprocessing_summary(df_featured)
        
        print(f"\n✅ PREPROCESSING COMPLETED")
        print(f"   • Final records: {summary['total_records']:,}")
        print(f"   • Store-product combinations: {summary['stores']} × {summary['products']}")
        print(f"   • Average daily sales: {summary['avg_sales']:.1f} ± {summary['sales_std']:.1f}")
        print(f"   • Selected features: {summary['selected_features']}")
        
        return df_featured, preprocessor, summary
        
    except Exception as e:
        print(f"❌ Preprocessing failed: {str(e)}")
        import traceback
        traceback.print_exc()
        return None, None, None

if __name__ == "__main__":
    # Test the preprocessing
    df, preprocessor, summary = diagnose_and_preprocess('data/retail_inventory_data.csv')
    
    if df is not None:
        print("\n🎯 Ready for improved LSTM training!")
    else:
        print("\n❌ Preprocessing failed - check data quality issues above")