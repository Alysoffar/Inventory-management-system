import subprocess
import sys
packages = ['tensorflow', 'pandas', 'numpy', 'scikit-learn', 'matplotlib', 'seaborn', 'joblib']
for package in packages:
    subprocess.check_call([sys.executable, '-m', 'pip', 'install', package])
    print(f'Installed {package}')