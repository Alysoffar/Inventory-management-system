"""
Root Cause Analysis for Poor Model Performance
User: Alysoffar  
Current Date: 2025-01-09 22:10:03 UTC
"""

import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns

def analyze_data_problems(file_path):
    """
    Deep dive into why the model is failing
    """
    print("🔍 ROOT CAUSE ANALYSIS")
    print("=" * 60)
    print(f"👤 User: Alysoffar")
    print(f"📅 Analysis Time: 2025-01-09 22:10:03 UTC")
    print("=" * 60)
    
    # Load data
    df = pd.read_csv(file_path)
    df['Date'] = pd.to_datetime(df['Date'])
    print(f"📊 Loaded {len(df):,} records")
    
    # 1. BASIC DATA OVERVIEW
    print(f"\n1️⃣ BASIC DATA OVERVIEW:")
    print(f"   • Date range: {df['Date'].min()} to {df['Date'].max()}")
    print(f"   • Unique stores: {df['Store ID'].nunique()}")
    print(f"   • Unique products: {df['Product ID'].nunique()}")
    print(f"   • Total days: {(df['Date'].max() - df['Date'].min()).days}")
    
    # 2. TARGET VARIABLE ANALYSIS
    print(f"\n2️⃣ TARGET VARIABLE ANALYSIS (Units Sold):")
    target_stats = df['Units Sold'].describe()
    print(f"   • Mean: {target_stats['mean']:.2f}")
    print(f"   • Std Dev: {target_stats['std']:.2f}")
    print(f"   • Min: {target_stats['min']:.2f}")
    print(f"   • Max: {target_stats['max']:.2f}")
    print(f"   • Zeros: {(df['Units Sold'] == 0).sum():,} ({(df['Units Sold'] == 0).mean()*100:.1f}%)")
    
    # Check if target has any variance
    if target_stats['std'] < 1.0:
        print("   ❌ CRITICAL: Very low variance in target variable!")
    
    # 3. MISSING VALUES CHECK
    print(f"\n3️⃣ MISSING VALUES:")
    missing = df.isnull().sum()
    for col, count in missing[missing > 0].items():
        print(f"   • {col}: {count:,} ({count/len(df)*100:.1f}%)")
    
    if missing.sum() == 0:
        print("   ✅ No missing values found")
    
    # 4. DATA DISTRIBUTION PROBLEMS
    print(f"\n4️⃣ DATA DISTRIBUTION ANALYSIS:")
    
    # Check for extreme skewness
    skewness = df['Units Sold'].skew()
    print(f"   • Skewness: {skewness:.2f}")
    if abs(skewness) > 2:
        print("   ⚠️  Highly skewed data detected")
    
    # Check for outliers
    Q1 = df['Units Sold'].quantile(0.25)
    Q3 = df['Units Sold'].quantile(0.75)
    IQR = Q3 - Q1
    outliers = df[(df['Units Sold'] < Q1 - 1.5*IQR) | (df['Units Sold'] > Q3 + 1.5*IQR)]
    print(f"   • Outliers: {len(outliers):,} ({len(outliers)/len(df)*100:.1f}%)")
    
    # 5. FEATURE CORRELATION CHECK
    print(f"\n5️⃣ FEATURE CORRELATION WITH TARGET:")
    numeric_cols = ['Inventory Level', 'Price', 'Demand Forecast', 'Units Sold']
    corr_matrix = df[numeric_cols].corr()['Units Sold'].abs().sort_values(ascending=False)
    
    print("   Top correlations with Units Sold:")
    for feature, corr in corr_matrix.items():
        if feature != 'Units Sold':
            status = "✅" if corr > 0.3 else "⚠️" if corr > 0.1 else "❌"
            print(f"   • {feature}: {corr:.3f} {status}")
    
    max_corr = corr_matrix.drop('Units Sold').max()
    if max_corr < 0.1:
        print("   ❌ CRITICAL: No features have meaningful correlation with target!")
    
    # 6. TEMPORAL PATTERNS
    print(f"\n6️⃣ TEMPORAL PATTERN ANALYSIS:")
    
    # Check if data has time-based patterns
    daily_avg = df.groupby('Date')['Units Sold'].mean()
    temporal_variance = daily_avg.var()
    print(f"   • Daily variance: {temporal_variance:.2f}")
    
    # Monthly patterns
    monthly_avg = df.groupby(df['Date'].dt.month)['Units Sold'].mean()
    monthly_variance = monthly_avg.var()
    print(f"   • Monthly variance: {monthly_variance:.2f}")
    
    if temporal_variance < 1 and monthly_variance < 1:
        print("   ❌ CRITICAL: No temporal patterns detected!")
    
    # 7. STORE-PRODUCT ANALYSIS
    print(f"\n7️⃣ STORE-PRODUCT COMBINATION ANALYSIS:")
    
    combo_stats = df.groupby(['Store ID', 'Product ID']).agg({
        'Units Sold': ['count', 'mean', 'std']
    }).round(2)
    combo_stats.columns = ['Records', 'Mean_Sales', 'Std_Sales']
    combo_stats = combo_stats.reset_index()
    
    print(f"   • Total combinations: {len(combo_stats)}")
    print(f"   • Avg records per combination: {combo_stats['Records'].mean():.1f}")
    print(f"   • Combinations with <20 records: {(combo_stats['Records'] < 20).sum()}")
    print(f"   • Combinations with zero variance: {(combo_stats['Std_Sales'] == 0).sum()}")
    
    insufficient_data = (combo_stats['Records'] < 20).sum()
    zero_variance = (combo_stats['Std_Sales'] == 0).sum()
    
    if insufficient_data > len(combo_stats) * 0.5:
        print("   ❌ CRITICAL: Most combinations have insufficient data!")
    
    if zero_variance > 0:
        print(f"   ❌ CRITICAL: {zero_variance} combinations have no sales variance!")
    
    # 8. CREATE DIAGNOSTIC VISUALIZATIONS
    print(f"\n8️⃣ CREATING DIAGNOSTIC PLOTS...")
    
    plt.figure(figsize=(20, 15))
    plt.suptitle('Data Quality Diagnostic Report - Alysoffar (2025-01-09)', fontsize=16, fontweight='bold')
    
    # Plot 1: Sales distribution
    plt.subplot(3, 4, 1)
    plt.hist(df['Units Sold'], bins=50, alpha=0.7, edgecolor='black')
    plt.title('Units Sold Distribution')
    plt.xlabel('Units Sold')
    plt.ylabel('Frequency')
    
    # Plot 2: Sales over time
    plt.subplot(3, 4, 2)
    daily_sales = df.groupby('Date')['Units Sold'].mean()
    plt.plot(daily_sales.index, daily_sales.values)
    plt.title('Average Daily Sales Over Time')
    plt.xticks(rotation=45)
    
    # Plot 3: Box plot by store
    plt.subplot(3, 4, 3)
    df.boxplot(column='Units Sold', by='Store ID', ax=plt.gca())
    plt.title('Sales Distribution by Store')
    plt.suptitle('')  # Remove default boxplot title
    
    # Plot 4: Correlation heatmap
    plt.subplot(3, 4, 4)
    sns.heatmap(corr_matrix.to_frame(), annot=True, cmap='coolwarm', center=0)
    plt.title('Feature Correlations')
    
    # Plot 5: Monthly patterns
    plt.subplot(3, 4, 5)
    monthly_sales = df.groupby(df['Date'].dt.month)['Units Sold'].mean()
    plt.bar(monthly_sales.index, monthly_sales.values)
    plt.title('Average Sales by Month')
    plt.xlabel('Month')
    
    # Plot 6: Day of week patterns
    plt.subplot(3, 4, 6)
    dow_sales = df.groupby(df['Date'].dt.dayofweek)['Units Sold'].mean()
    days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
    plt.bar(days, dow_sales.values)
    plt.title('Average Sales by Day of Week')
    plt.xticks(rotation=45)
    
    # Plot 7: Records per combination
    plt.subplot(3, 4, 7)
    plt.hist(combo_stats['Records'], bins=20, alpha=0.7, edgecolor='black')
    plt.title('Records per Store-Product')
    plt.xlabel('Number of Records')
    
    # Plot 8: Sales variance per combination
    plt.subplot(3, 4, 8)
    plt.hist(combo_stats['Std_Sales'].dropna(), bins=20, alpha=0.7, edgecolor='black')
    plt.title('Sales Variance per Combination')
    plt.xlabel('Standard Deviation')
    
    # Plot 9: Price vs Sales scatter
    plt.subplot(3, 4, 9)
    sample_df = df.sample(min(1000, len(df)))  # Sample for visibility
    plt.scatter(sample_df['Price'], sample_df['Units Sold'], alpha=0.5)
    plt.title('Price vs Sales')
    plt.xlabel('Price')
    plt.ylabel('Units Sold')
    
    # Plot 10: Inventory vs Sales scatter
    plt.subplot(3, 4, 10)
    plt.scatter(sample_df['Inventory Level'], sample_df['Units Sold'], alpha=0.5)
    plt.title('Inventory vs Sales')
    plt.xlabel('Inventory Level')
    plt.ylabel('Units Sold')
    
    # Plot 11: Demand forecast vs actual
    plt.subplot(3, 4, 11)
    plt.scatter(sample_df['Demand Forecast'], sample_df['Units Sold'], alpha=0.5)
    plt.plot([sample_df['Demand Forecast'].min(), sample_df['Demand Forecast'].max()],
             [sample_df['Demand Forecast'].min(), sample_df['Demand Forecast'].max()], 
             'r--', alpha=0.8)
    plt.title('Demand Forecast vs Actual')
    plt.xlabel('Demand Forecast')
    plt.ylabel('Actual Sales')
    
    # Plot 12: Sales by product
    plt.subplot(3, 4, 12)
    product_sales = df.groupby('Product ID')['Units Sold'].mean()
    plt.bar(range(len(product_sales)), product_sales.values)
    plt.title('Average Sales by Product')
    plt.xlabel('Product Index')
    plt.xticks([])
    
    plt.tight_layout()
    plt.savefig('data_quality_diagnosis.png', dpi=300, bbox_inches='tight')
    plt.show()
    
    # 9. FINAL DIAGNOSIS
    print(f"\n9️⃣ FINAL DIAGNOSIS AND RECOMMENDATIONS:")
    print(f"{'='*60}")
    
    issues = []
    if target_stats['std'] < 5:
        issues.append("Low variance in target variable")
    if max_corr < 0.1:
        issues.append("No predictive features")
    if insufficient_data > len(combo_stats) * 0.3:
        issues.append("Insufficient data per store-product")
    if zero_variance > 0:
        issues.append("Some products have constant sales")
    if temporal_variance < 1:
        issues.append("No temporal patterns")
    
    if not issues:
        print("✅ Data quality appears adequate - model architecture issue")
    else:
        print("❌ DATA QUALITY ISSUES IDENTIFIED:")
        for i, issue in enumerate(issues, 1):
            print(f"   {i}. {issue}")
    
    print(f"\n💡 RECOMMENDED ACTIONS:")
    print(f"   1. Focus on products with high variance (std > 5)")
    print(f"   2. Filter to store-product combinations with 30+ records")
    print(f"   3. Remove products with zero sales variance")
    print(f"   4. Create stronger features (ratios, trends, seasonality)")
    print(f"   5. Consider simpler models (Linear Regression, Random Forest)")
    
    return df, combo_stats, issues

if __name__ == "__main__":
    df, combo_stats, issues = analyze_data_problems(r'D:\WORK\Findo\AIMODEL\data\retail_store_inventory.csv')
    
    if len(issues) > 3:
        print(f"\n🚨 CRITICAL: Too many data quality issues for LSTM to work!")
        print(f"   Recommend fixing data quality first.")
    else:
        print(f"\n🟡 Data has some issues but might be workable with better preprocessing.")