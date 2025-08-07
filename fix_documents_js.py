#!/usr/bin/env python3
"""
Скрипт для исправления JavaScript ошибок в файле documents-tab.blade.php
"""

import re

def fix_javascript_errors():
    file_path = r'c:\OSPanel\domains\rem\resources\views\documents\partials\documents-tab.blade.php'
    
    # Читаем файл
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Исправляем все обращения к loadTabContent
    content = re.sub(
        r'typeof loadTabContent === \'function\'',
        'typeof window.loadTabContent === \'function\'',
        content
    )
    
    content = re.sub(
        r'loadTabContent\(currentTab,',
        'window.loadTabContent(currentTab,',
        content
    )
    
    # Записываем обратно
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print("JavaScript ошибки исправлены!")

if __name__ == '__main__':
    fix_javascript_errors()
