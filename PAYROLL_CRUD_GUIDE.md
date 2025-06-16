# Payroll CRUD System - User Guide

## Overview
This document provides instructions for using the complete Payroll CRUD (Create, Read, Update, Delete) system with import functionality.

## Features
- ✅ Create new payroll records
- ✅ View and edit existing payroll records  
- ✅ Delete individual or multiple payroll records
- ✅ Import payroll data from Excel/CSV files
- ✅ SMS status management
- ✅ Real-time data validation
- ✅ Multi-language support (English/Korean)

## Getting Started

### 1. Accessing the Payroll System
Navigate to: `http://127.0.0.1:8001/payrolls`

### 2. Creating New Payroll Records

#### Method 1: Manual Entry
1. Click the **"새 급여 정보 생성"** (Create New) button in the actions bar
2. Fill in the required fields:
   - Employee ID (사원번호)
   - Employee Name (직원명)
   - Department (부서)
   - Position (직책)
   - Phone Number (전화번호) - optional
   - Work Days (근무일수)
   - Base Salary (기본급)
   - Allowances (수당) - optional
   - Deductions (공제) - optional
   - Remarks (비고) - optional
3. Gross Pay and Net Pay will be calculated automatically
4. Click **"저장"** (Save) to create the record

#### Method 2: Import from File
1. Click the **"가져오기"** (Import) button in the actions bar
2. Select an Excel (.xlsx, .xls) or CSV file
3. Supported file format:
   ```
   사원번호,부서,직책,이름,근무일수,기본급,수당총계,총지급액,공제총계,실수령액,비고,SMS상태,전화번호
   ```
4. Preview the data and click **"가져오기"** (Import) to confirm
5. A sample file is available at: `/public/sample_payroll_data.csv`

### 3. Viewing and Editing Records

#### View Details
- Click on any **Employee ID** to view attendance details
- Click on any **Net Pay** amount to view payroll details with allowances and deductions breakdown

#### Edit Records
- Method 1: Right-click on any row and select **"수정"** (Edit)
- Method 2: Open payroll details and click **"수정하기"** (Modify) button
- Method 3: Use the context menu for quick actions

### 4. Deleting Records

#### Single Delete
- Right-click on a row and select **"삭제"** (Delete)
- Confirm the deletion in the popup dialog

#### Bulk Delete
- Select multiple rows using checkboxes
- Press **Ctrl + D** (keyboard shortcut)
- Or use the bulk delete option from the context menu

### 5. SMS Management
1. Select employees using checkboxes
2. Click **"선택 대상자에게 문자 발송"** (Send SMS to Selected)
3. View SMS preview and details
4. Update SMS status as needed

### 6. Search and Filtering
- Use the search box to find specific records
- Apply filters using the filter buttons (SETEC, month)
- Use column filters for advanced searching

## Keyboard Shortcuts
- **Ctrl + D**: Bulk delete selected records
- **Right-click**: Context menu with edit/delete options

## Data Validation
- All required fields are validated before saving
- Numeric fields (salary, allowances, deductions) must be valid numbers
- Work days must be between 0-31
- Employee ID must be unique

## File Import Requirements

### Supported Formats
- Excel: .xlsx, .xls
- CSV: .csv (UTF-8 encoding recommended)

### CSV Column Headers (Korean)
```
사원번호,부서,직책,이름,근무일수,기본급,수당총계,총지급액,공제총계,실수령액,비고,SMS상태,전화번호
```

### CSV Column Headers (English)
```
EmpID,Department,Position,Name,WorkDays,BaseSalary,Allowances,GrossPay,Deductions,NetPay,Remarks,SMSStatus,PhoneNumber
```

### Sample Data Format
```csv
TEST001,개발팀,주임,김개발,22,2500000,300000,2800000,280000,2520000,신입사원,pending,010-1111-2222
```

## Error Handling
- Form validation errors are displayed in real-time
- Import errors show detailed feedback
- Network errors display user-friendly messages
- All operations use SweetAlert2 for consistent notifications

## Technical Details

### Database Schema
The payrolls table includes:
- `uid`: Unique identifier
- `employee_id`: Employee ID (string)
- `department`: Department name
- `position`: Job position
- `name`: Employee name
- `phone_number`: Contact number
- `work_days`: Number of working days
- `base_salary`: Base salary amount (decimal)
- `allowances`: Total allowances (decimal)
- `gross_pay`: Calculated gross pay (decimal)
- `deductions`: Total deductions (decimal)
- `net_pay`: Calculated net pay (decimal)
- `remarks`: Additional notes
- `sms_sent_status`: SMS delivery status
- `allowance_items`: JSON array of allowance details
- `deduction_items`: JSON array of deduction details
- Timestamps: created_at, updated_at

### API Endpoints
- `GET /payrolls` - List all payrolls (AJAX)
- `POST /payrolls` - Create new payroll
- `PUT /payrolls/{id}` - Update existing payroll
- `DELETE /payrolls/{id}` - Delete single payroll
- `DELETE /payrolls/bulk-delete` - Delete multiple payrolls
- `POST /payrolls/import` - Import from file
- `POST /payrolls/update-sms-status` - Update SMS status

### Language Support
- Korean (kr): Default language with full translations
- English (en): Complete English translations available
- Switch language in application settings

## Troubleshooting

### Common Issues
1. **Import fails**: Check file format and encoding (UTF-8 recommended)
2. **Validation errors**: Ensure all required fields are filled
3. **SMS not updating**: Check selected rows before attempting SMS operations
4. **Grid not loading**: Check browser console for JavaScript errors

### Browser Requirements
- Modern browsers (Chrome, Firefox, Safari, Edge)
- JavaScript enabled
- Local storage enabled for preferences

## Support
For technical support or feature requests, please contact the development team.

---
**Last Updated**: June 11, 2025
**Version**: 1.0.0
