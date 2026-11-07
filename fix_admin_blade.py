#!/usr/bin/env python3
"""
Fix corrupted template literals in admin.blade.php
The file has broken $ { instead of ${ throughout
"""
import sys
import os

filepath = r'c:\Users\gilan\Herd\Project_UMKM\resources\views\dashboard\admin.blade.php'

print(f"Reading file: {filepath}")
try:
    with open(filepath, 'r', encoding='utf-8') as f:
        original = f.read()
    
    print(f"File size: {len(original)} bytes")
    
    # Count broken patterns
    broken_count = original.count('$ {')
    print(f"Found {broken_count} instances of '$ {{' that need fixing")
    
    # Fix the corruption
    fixed = original.replace('$ {', '${')
    
    # Verify the fix
    remaining = fixed.count('$ {')
    print(f"After fix: {remaining} instances remaining")
    
    # Write back
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(fixed)
    
    print("✓ File fixed successfully!")
    print(f"Fixed {broken_count} template literal issues")
    
except Exception as e:
    print(f"✗ Error: {e}")
    sys.exit(1)
