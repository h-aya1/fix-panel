<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('payroll.payslip.page_title', ['year' => $payslipData['year'], 'month' => $payslipData['month_name']]) }}</title>
    
    
    <style>
        :root {
            --primary-color: #333;
            --secondary-color: #666;
            --border-color: #000;
            --light-border: #777;
            --background-light: #f0f2f5;
            --background-white: #fff;
            --background-gray: #e8e8e8;
            --background-light-gray: #f9f9f9;
            --highlight-yellow: #ffd700;
            --danger-red: #d9534f;
            --shadow-light: 0 2px 8px rgba(0,0,0,0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Malgun Gothic', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: var(--background-light);
            color: var(--primary-color);
            font-size: 12px;
            line-height: 1.4;
        }

        .payslip-container {
            max-width: 900px;
            margin: 30px auto;
            background-color: var(--background-white);
            box-shadow: var(--shadow-light);
            border-radius: 8px;
            overflow: hidden;
        }

        /* Header Section */
        .payslip-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            background-color: var(--background-white);
            border-bottom: 1px solid #ddd;
        }

        .btn {
            display: inline-block;
            padding: 8px 20px;
            border: 1px solid var(--secondary-color);
            border-radius: 6px;
            background-color: var(--background-white);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            color: var(--primary-color);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
        }

        .btn-logout {
            border-color: var(--danger-red);
            background-color: var(--danger-red);
            color: var(--background-white);
        }

        .btn-logout:hover {
            background-color: #c9302c;
        }

        /* Content Section */
        .payslip-content {
            padding: 30px;
            position: relative;
        }

        /* Period Selectors */
        .period-selectors {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .period-box {
            min-width: 120px;
            padding: 12px 20px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            background-color: var(--background-white);
        }

        /* Title */
        .main-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .title-text {
            display: inline-block;
            border: 2px solid var(--border-color);
            padding: 15px 30px;
            font-size: 20px;
            font-weight: bold;
            background-color: var(--background-white);
        }

        /* Employee Info Layout */
        .employee-info-layout {
            display: grid;
            grid-template-columns: 250px 1fr 220px;
            gap: 20px;
            align-items: start;
            margin-bottom: 25px;
        }

        /* Employee Basic Info */
        .employee-basic-info {
            display: flex;
            flex-direction: column;
        }

        .info-row {
            display: flex;
            border: 1px solid var(--border-color);
            border-bottom: none;
        }

        .info-row:last-child {
            border-bottom: 1px solid var(--border-color);
        }

        .info-label {
            background-color: var(--background-gray);
            padding: 10px 12px;
            font-weight: bold;
            text-align: center;
            border-right: 1px solid var(--border-color);
            min-width: 90px;
            font-size: 12px;
        }

        .info-value {
            padding: 10px 15px;
            flex: 1;
            font-size: 12px;
            text-align: right;
        }

        .net-pay-row .info-label {
            background-color: var(--highlight-yellow);
            font-size: 13px;
        }

        .net-pay-row .info-value {
            font-weight: bold;
            font-size: 13px;
        }

        /* Additional Info (Name, Payment Date) */
        .additional-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .additional-info-item {
            display: flex;
            border: 1px solid var(--border-color);
        }

        .additional-info-item .info-label {
            min-width: 70px;
            font-size: 11px;
        }

        .additional-info-item .info-value {
            text-align: left;
            font-size: 11px;
        }

        /* Main Table */
        .payslip-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid var(--border-color);
            margin-top: 20px;
        }

        .payslip-table th,
        .payslip-table td {
            border: 1px solid var(--light-border);
            padding: 8px 10px;
            font-size: 12px;
        }

        .payslip-table thead th {
            background-color: var(--background-gray);
            font-weight: bold;
            text-align: center;
            font-size: 14px;
            padding: 12px 10px;
        }

        .group-header {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            background-color: #f5f5f5;
            font-weight: bold;
            width: 35px;
            text-align: center;
            vertical-align: middle;
            font-size: 11px;
        }

        .item-label {
            background-color: var(--background-light-gray);
            text-align: left;
            padding-left: 12px;
            width: 130px;
        }

        .amount-cell {
            text-align: right;
            padding-right: 12px;
            width: 100px;
            font-family: 'Courier New', monospace;
        }

        .total-row {
            background-color: var(--background-gray);
            font-weight: bold;
        }

        .total-row td {
            font-size: 13px;
        }

        .total-label {
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .payslip-container {
                margin: 10px;
            }
            
            .payslip-content {
                padding: 20px;
            }
            
            .employee-info-layout {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .period-selectors {
                flex-direction: column;
                align-items: center;
            }
            
            .payslip-table {
                font-size: 10px;
            }
        }

        /* Print Styles */
        @media print {
            body {
                background-color: var(--background-white);
                font-size: 10pt;
                padding: 0;
                margin: 0;
            }

            .payslip-container {
                box-shadow: none;
                border-radius: 0;
                margin: 0;
            }

            .payslip-header {
                display: none;
            }

            .payslip-content {
                padding: 15px;
            }

            .payslip-table th,
            .payslip-table td {
                padding: 4px 6px;
                font-size: 9pt;
            }

            .btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="payslip-container">
        {{-- Header with action buttons --}}
        <header class="payslip-header">
            <button type="button" class="btn" onclick="printPayslip()">
                {{ __('payroll.payslip.download_pdf_button') }}
            </button>
            
            
                @csrf
                <button type="submit" class="btn btn-logout">
                    {{ __('payroll.payslip.logout_button') }}
                </button>
            
        </header>

        {{-- Main content --}}
        <main class="payslip-content">
            {{-- Period selectors --}}
            <section class="period-selectors">
                <div class="period-box">{{ $payslipData['year'] }}{{ __('payroll.common.year_suffix') }}</div>
                <div class="period-box">{{ $payslipData['month_numeric'] }}{{ __('payroll.common.month_suffix') }}</div>
            </section>

            {{-- Main title --}}
            <section class="main-title">
                <h1 class="title-text">
                    {{ __('payroll.payslip.payslip_main_title', [
                        'year' => $payslipData['year'], 
                        'month' => $payslipData['month_numeric']
                    ]) }}
                </h1>
            </section>

            {{-- Employee information layout --}}
            <section class="employee-info-layout">
                {{-- Basic employee info --}}
                <div class="employee-basic-info">
                    <div class="info-row">
                        <span class="info-label">{{ __('payroll.payslip.employee_id_label') }}</span>
                        <span class="info-value">{{ $payslipData['employee_id'] }}</span>
                    </div>
                    <div class="info-row net-pay-row">
                        <span class="info-label">{{ __('payroll.payslip.net_pay_amount_label') }}</span>
                        <span class="info-value">{{ number_format($payslipData['net_pay_amount']) }}</span>
                    </div>
                </div>

                {{-- Spacer --}}
                <div></div>

                {{-- Additional info (Name, Payment Date) --}}
                <div class="additional-info">
                    <div class="additional-info-item">
                        <span class="info-label">{{ __('payroll.payslip.name_label') }}</span>
                        <span class="info-value">{{ $payslipData['employee_name'] }}</span>
                    </div>
                    <div class="additional-info-item">
                        <span class="info-label">{{ __('payroll.payslip.payment_date_label') }}</span>
                        <span class="info-value">{{ $payslipData['payment_date'] }}</span>
                    </div>
                </div>
            </section>

            {{-- Main payslip table --}}
            <section class="payslip-table-section">
                <table class="payslip-table">
                    <thead>
                        <tr>
                            <th colspan="2">{{ __('payroll.payslip.earnings_section_title') }}</th>
                            <th colspan="2">{{ __('payroll.payslip.deductions_section_title') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $maxRows = max(
                                count($payslipData['earnings']['regular'] ?? []) + count($payslipData['earnings']['irregular'] ?? []),
                                count($payslipData['deductions'] ?? [])
                            );
                            $regularEarningsCount = count($payslipData['earnings']['regular'] ?? []);
                            $irregularEarningsCount = count($payslipData['earnings']['irregular'] ?? []);
                            $totalEarningsRows = $regularEarningsCount + $irregularEarningsCount;
                            $deductionsCount = count($payslipData['deductions'] ?? []);
                        @endphp

                        @for ($i = 0; $i < $maxRows; $i++)
                            <tr>
                                {{-- Earnings Side --}}
                                @if ($i === 0 && $regularEarningsCount > 0)
                                    <td rowspan="{{ $regularEarningsCount }}" class="group-header">
                                        {{ __('payroll.payslip.regular_earnings_group') }}
                                    </td>
                                @elseif ($i === $regularEarningsCount && $irregularEarningsCount > 0)
                                    <td rowspan="{{ $irregularEarningsCount }}" class="group-header">
                                        {{ __('payroll.payslip.irregular_earnings_group') }}
                                    </td>
                                @endif

                                @if ($i < $regularEarningsCount)
                                    {{-- Regular earnings --}}
                                    <td class="item-label">
                                        {{ __($payslipData['earnings']['regular'][$i]['label_key']) }}
                                    </td>
                                    <td class="amount-cell">
                                        {{ $payslipData['earnings']['regular'][$i]['value'] ? number_format($payslipData['earnings']['regular'][$i]['value']) : '' }}
                                    </td>
                                @elseif ($i < $totalEarningsRows)
                                    {{-- Irregular earnings --}}
                                    @php $irregularIndex = $i - $regularEarningsCount; @endphp
                                    <td class="item-label">
                                        {{ $payslipData['earnings']['irregular'][$irregularIndex]['label_key'] ? __($payslipData['earnings']['irregular'][$irregularIndex]['label_key']) : '' }}
                                    </td>
                                    <td class="amount-cell">
                                        {{ $payslipData['earnings']['irregular'][$irregularIndex]['value'] ? number_format($payslipData['earnings']['irregular'][$irregularIndex]['value']) : '' }}
                                    </td>
                                @else
                                    {{-- Empty earnings cells --}}
                                    <td class="item-label"></td>
                                    <td class="amount-cell"></td>
                                @endif

                                {{-- Deductions Side --}}
                                @if ($i < $deductionsCount)
                                    <td class="item-label">
                                        {{ $payslipData['deductions'][$i]['label_key'] ? __($payslipData['deductions'][$i]['label_key']) : '' }}
                                    </td>
                                    <td class="amount-cell">
                                        {{ $payslipData['deductions'][$i]['value'] ? number_format($payslipData['deductions'][$i]['value']) : '' }}
                                    </td>
                                @else
                                    {{-- Empty deduction cells --}}
                                    <td class="item-label"></td>
                                    <td class="amount-cell"></td>
                                @endif
                            </tr>
                        @endfor

                        {{-- Total row --}}
                        <tr class="total-row">
                            <td colspan="2" class="total-label">{{ __('payroll.payslip.total_label') }}</td>
                            <td class="amount-cell">{{ number_format($payslipData['total_earnings']) }}</td>
                            <td class="total-label">{{ __('payroll.payslip.total_label') }}</td>
                            <td class="amount-cell">{{ number_format($payslipData['total_deductions']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    @push('scripts')
    <script>
        function printPayslip() {
            // Hide non-printable elements
            const header = document.querySelector('.payslip-header');
            if (header) {
                header.style.display = 'none';
            }
            
            // Print the page
            window.print();
            
            // Restore elements after printing
            setTimeout(() => {
                if (header) {
                    header.style.display = 'flex';
                }
            }, 1000);
        }

        // Handle print completion
        window.addEventListener('afterprint', function() {
            const header = document.querySelector('.payslip-header');
            if (header) {
                header.style.display = 'flex';
            }
        });

        // Add loading state to buttons
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function() {
                if (!this.classList.contains('btn-logout')) {
                    this.textContent = '{{ __("payroll.common.processing") }}...';
                    this.disabled = true;
                }
            });
        });
    </script>
    @endpush
</body>
</html>