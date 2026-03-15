#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import zipfile
import os
import shutil
import plistlib
import tempfile
import json
import sys
from pathlib import Path

class IPAModifier:
    def __init__(self):
        self.temp_dir = None
        
    def extract_ipa(self, ipa_path):
        """استخراج ملف IPA إلى مجلد مؤقت"""
        self.temp_dir = tempfile.mkdtemp()
        
        with zipfile.ZipFile(ipa_path, 'r') as zip_ref:
            zip_ref.extractall(self.temp_dir)
        
        # البحث عن مجلد Payload
        payload_dir = os.path.join(self.temp_dir, 'Payload')
        if not os.path.exists(payload_dir):
            raise Exception("لا يمكن العثور على مجلد Payload في ملف IPA")
        
        # البحث عن ملف .app
        app_files = [f for f in os.listdir(payload_dir) if f.endswith('.app')]
        if not app_files:
            raise Exception("لا يمكن العثور على مجلد .app في Payload")
        
        app_path = os.path.join(payload_dir, app_files[0])
        return app_path
    
    def read_info_plist(self, app_path):
        """قراءة ملف Info.plist"""
        plist_path = os.path.join(app_path, 'Info.plist')
        
        if not os.path.exists(plist_path):
            raise Exception("لا يمكن العثور على ملف Info.plist")
        
        with open(plist_path, 'rb') as f:
            plist_data = plistlib.load(f)
        
        return plist_data
    
    def modify_info_plist(self, app_path, new_bundle_id=None, new_name=None, new_display_name=None):
        """تعديل ملف Info.plist"""
        plist_path = os.path.join(app_path, 'Info.plist')
        
        # قراءة الملف الحالي
        with open(plist_path, 'rb') as f:
            plist_data = plistlib.load(f)
        
        # حفظ القيم القديمة
        old_values = {
            'bundle_id': plist_data.get('CFBundleIdentifier', ''),
            'name': plist_data.get('CFBundleName', ''),
            'display_name': plist_data.get('CFBundleDisplayName', '')
        }
        
        # تطبيق التعديلات
        if new_bundle_id:
            plist_data['CFBundleIdentifier'] = new_bundle_id
        
        if new_name:
            plist_data['CFBundleName'] = new_name
        
        if new_display_name:
            plist_data['CFBundleDisplayName'] = new_display_name
        
        # حفظ الملف المعدل
        with open(plist_path, 'wb') as f:
            plistlib.dump(plist_data, f)
        
        return old_values, plist_data
    
    def create_new_ipa(self, original_ipa_path, output_path=None):
        """إنشاء ملف IPA جديد من المجلد المؤقت"""
        if not output_path:
            base_name = os.path.basename(original_ipa_path)
            name_without_ext = os.path.splitext(base_name)[0]
            output_path = f"modified/{name_without_ext}_modified.ipa"
        
        # التأكد من وجود مجلد modified
        os.makedirs('modified', exist_ok=True)
        
        # إنشاء ملف IPA جديد
        with zipfile.ZipFile(output_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
            for root, dirs, files in os.walk(self.temp_dir):
                for file in files:
                    file_path = os.path.join(root, file)
                    arcname = os.path.relpath(file_path, self.temp_dir)
                    zipf.write(file_path, arcname)
        
        return output_path
    
    def cleanup(self):
        """تنظيف الملفات المؤقتة"""
        if self.temp_dir and os.path.exists(self.temp_dir):
            shutil.rmtree(self.temp_dir)
    
    def read_only(self, ipa_path):
        """قراءة معلومات IPA فقط بدون تعديل"""
        try:
            app_path = self.extract_ipa(ipa_path)
            plist_data = self.read_info_plist(app_path)
            
            result = {
                'success': True,
                'current_bundle': plist_data.get('CFBundleIdentifier', ''),
                'current_name': plist_data.get('CFBundleName', ''),
                'current_display_name': plist_data.get('CFBundleDisplayName', ''),
                'version': plist_data.get('CFBundleShortVersionString', ''),
                'build': plist_data.get('CFBundleVersion', '')
            }
            
            self.cleanup()
            return result
            
        except Exception as e:
            self.cleanup()
            return {
                'success': False,
                'message': str(e)
            }
    
    def modify(self, ipa_path, new_bundle_id, new_name, new_display_name):
        """تعديل ملف IPA"""
        try:
            app_path = self.extract_ipa(ipa_path)
            old_values, new_values = self.modify_info_plist(
                app_path, 
                new_bundle_id if new_bundle_id else None,
                new_name if new_name else None,
                new_display_name if new_display_name else None
            )
            
            output_path = self.create_new_ipa(ipa_path)
            
            result = {
                'success': True,
                'message': 'تم تعديل الملف بنجاح',
                'output_file': output_path,
                'old_values': old_values,
                'new_values': {
                    'bundle_id': new_values.get('CFBundleIdentifier', ''),
                    'name': new_values.get('CFBundleName', ''),
                    'display_name': new_values.get('CFBundleDisplayName', '')
                }
            }
            
            self.cleanup()
            return result
            
        except Exception as e:
            self.cleanup()
            return {
                'success': False,
                'message': str(e)
            }

def main():
    if len(sys.argv) < 2:
        print(json.dumps({
            'success': False,
            'message': 'استخدام: modify_ipa.py [read|modify] [ملف_IPA] [خيارات...]'
        }))
        return
    
    modifier = IPAModifier()
    command = sys.argv[1]
    
    if command == 'read':
        if len(sys.argv) < 3:
            print(json.dumps({
                'success': False,
                'message': 'يرجى تحديد ملف IPA'
            }))
            return
        
        ipa_path = sys.argv[2]
        result = modifier.read_only(ipa_path)
        print(json.dumps(result, ensure_ascii=False))
        
    elif command == 'modify':
        if len(sys.argv) < 6:
            print(json.dumps({
                'success': False,
                'message': 'يرجى تحديد: ملف IPA، البundle الجديد، الاسم الجديد، اسم العرض الجديد'
            }))
            return
        
        ipa_path = sys.argv[2]
        new_bundle_id = sys.argv[3] if sys.argv[3] != '""' and sys.argv[3] else None
        new_name = sys.argv[4] if sys.argv[4] != '""' and sys.argv[4] else None
        new_display_name = sys.argv[5] if sys.argv[5] != '""' and sys.argv[5] else None
        
        result = modifier.modify(ipa_path, new_bundle_id, new_name, new_display_name)
        print(json.dumps(result, ensure_ascii=False))
    
    else:
        print(json.dumps({
            'success': False,
            'message': 'أمر غير معروف'
        }))

if __name__ == "__main__":
    main()
