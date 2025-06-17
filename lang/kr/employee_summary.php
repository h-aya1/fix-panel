<?php

return [
    'title' => '직원 요약',
    'import_description' => '직원 요약 Excel/CSV 파일을 업로드하세요.',
    'select_file' => '파일 선택',
    'download_template' => '템플릿 다운로드',
    'importing' => '가져오는 중...',
    'please_wait' => '파일을 처리하는 동안 잠시 기다려주세요.',
    'import_successful_title' => '가져오기 성공',
    'import_successful' => ':count개의 레코드를 성공적으로 가져왔습니다!',
    'import_failed' => '가져오기에 실패했습니다. 파일 형식을 확인해주세요.',
    'preview_failed' => '파일 미리보기에 실패했습니다. 파일 형식을 확인해주세요.',
    'preview_title' => '가져오기 데이터 미리보기',
    'preview_description' => '가져오기 전에 아래 데이터를 검토하세요. 가져올 레코드를 선택할 수 있습니다.',
    'select_all' => '전체 선택',
    'deselect_all' => '전체 해제',
    'save_selected' => '선택 항목 저장',
    'cancel_import' => '취소',
    'selected_count' => '총 :total개 중 :count개 선택됨',
    'no_data_selected' => '가져올 레코드를 최소 하나 이상 선택해주세요.',
    'delete_all' => '전체 삭제',
    'confirm_delete_all' => '정말 삭제하시겠습니까?',
    'delete_all_warning' => '모든 직원 요약 레코드가 영구적으로 삭제됩니다. 이 작업은 되돌릴 수 없습니다.',
    'deleted_successfully' => '레코드 삭제됨',
    'delete_failed' => '레코드 삭제에 실패했습니다',
    'deleted_all_successful' => ':count개의 레코드를 성공적으로 삭제했습니다!',
    'delete_all_failed' => '전체 레코드 삭제에 실패했습니다',
    
    // Statistics
    'total_records' => '총 레코드: :count개',
    'latest_import' => '최근 가져오기: :date',
    'total_employees' => '총 직원 수',
    'total_base_salary' => '총 기본급',
    'total_net_payment' => '총 실지급액', 
    'avg_work_days' => '평균 근무일',
    'showing_records' => '총 :total개 중 :count개 표시',
    
    // Table headers
    'table' => [
        'employee_id' => '직원 ID',
        'name' => '이름',
        'company' => '회사명',
        'position' => '직급',
        'age' => '나이',
        'work_days' => '근무일',
        'base_salary' => '기본급',
        'total_earnings' => '총 지급액',
        'total_deductions' => '총 공제액',
        'net_payment' => '실지급액',
        'contact' => '연락처',
        'join_date' => '입사일',
        'imported_at' => '가져온 날짜',
    ],
    
    // Empty state
    'no_records' => '직원 요약 레코드가 없습니다',
    'no_records_description' => '첫 번째 직원 요약 파일을 가져와서 시작하세요.',
    'import_first_file' => '첫 파일 가져오기',
    
    // CRUD messages
    'created_successfully' => '직원 요약이 성공적으로 생성되었습니다!',
    'updated_successfully' => '직원 요약이 성공적으로 업데이트되었습니다!',
    'deleted_successfully' => '직원 요약이 성공적으로 삭제되었습니다!',
];
