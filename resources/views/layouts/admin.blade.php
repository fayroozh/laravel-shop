<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --bg-color: #f4f4f4;
            --card-bg: #ffffff;
            --text-color: #2c3e50;
            --border-color: #eee;
            --sidebar-width: 250px;
        }

        /* Search Section Styles */
        .search-section {
            margin-bottom: 25px;
            padding: 20px;
        }

        .search-fields {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }

        .form-group {
            margin-bottom: 0;
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 30px;
            margin: 30px 0;
        }

        .chart-card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .chart-card canvas {
            width: 100% !important;
            height: 300px !important;
        }

        .chart-card h3 {
            margin-bottom: 15px;
            color: var(--text-color);
        }

        /* Data Table Styles */
        .data-table {
            margin: 20px 0;
            background: var(--card-bg);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        .styled-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 20px 0;
        }

        .styled-table th,
        .styled-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .styled-table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
        }

        .styled-table tr:last-child td {
            border-bottom: none;
        }

        /* Status Colors */
        .status-high {
            color: var(--success-color);
        }

        .status-normal {
            color: var(--accent-color);
        }

        .status-low {
            color: var(--danger-color);
        }

        /* Type Colors */
        .type-new {
            color: var(--accent-color);
        }

        .type-regular {
            color: var(--success-color);
        }

        .type-vip {
            color: var(--warning-color);
        }

        /* Breadcrumb Navigation */
        .breadcrumb {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            background: var(--card-bg);
            border-radius: 8px;
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
        }

        .breadcrumb-item:not(:last-child)::after {
            content: '/';
            margin: 0 10px;
            color: var(--text-color);
            opacity: 0.5;
        }

        .breadcrumb-item a {
            color: var(--accent-color);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: var(--text-color);
        }

        [data-theme="dark"] {
            --primary-color: #1a252f;
            --secondary-color: #2c3e50;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #e67e22;
            --danger-color: #c0392b;
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --text-color: #ffffff;
            --border-color: #333;
        }

        /* تحسينات خاصة بصفحة الطلبات */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
            margin-left: 20px;
            /* Add space from sidebar */
        }

        .dashboard-header h1 {
            color: var(--primary-color);
            font-size: 28px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* تحسينات حالة الطلب */
        .order-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            min-width: 100px;
        }

        .status-pending {
            background-color: #f39c12;
            color: white;
        }

        .status-processing {
            background-color: #3498db;
            color: white;
        }

        .status-shipped {
            background-color: #9b59b6;
            color: white;
        }

        .status-delivered {
            background-color: #2ecc71;
            color: white;
        }

        .status-cancelled {
            background-color: #e74c3c;
            color: white;
        }

        /* تحسينات تنبيهات النجاح */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid transparent;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            border-left-color: #2ecc71;
            color: #27ae60;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            display: flex;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: all 0.3s ease;
            line-height: 1.6;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            min-height: 100vh;
            top: 0;
            left: 0;
            z-index: 1000;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .content {
            margin-left: 270px;
            /* Add more space from sidebar */
            padding: 20px;
            min-height: 100vh;
            background: #f8f9fa;
            flex: 1;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar h2 {
            font-size: 18px;
            margin: 0 20px 30px;
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            white-space: nowrap;
            /* Keep "Admin Dashboard" on one line */
            color: white;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            letter-spacing: 1px;
            display: flex;
            align-items: center;
        }

        .sidebar a {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 12px 20px;
            margin: 2px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
            position: relative;
            overflow: hidden;
            z-index: 1;
            backdrop-filter: blur(5px);
        }

        .sidebar a:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.5s ease;
            z-index: -1;
        }

        .sidebar a:hover:before {
            left: 100%;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.5px;
        }

        .content {
            margin-left: 270px;
            /* Add more space from sidebar */
            padding: 20px;
            min-height: 100vh;
            background: #f8f9fa;
            flex: 1;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
            margin-left: 20px;
            /* Add space from sidebar */
        }

        .dashboard-actions {
            display: flex;
            gap: 10px;
        }

        .btn-export,
        .btn-theme {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-pdf {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .btn-excel {
            background: linear-gradient(135deg, #27ae60, #229954);
            color: white;
        }

        .btn-theme {
            background: linear-gradient(135deg, #8e44ad, #9b59b6);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px 20px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin-top: 25px;
            width: 100%;
            letter-spacing: 0.5px;
        }

        .btn-theme:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
            letter-spacing: 1px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin: 30px 0;
            margin-left: 20px;
            /* Add space from sidebar */
        }

        .stat-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-bottom: 0;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-color), var(--success-color));
        }

        .stat-card.products::before {
            background: linear-gradient(90deg, #3498db, #2980b9);
        }

        .stat-card.orders::before {
            background: linear-gradient(90deg, #e74c3c, #c0392b);
        }

        .stat-card.employees::before {
            background: linear-gradient(90deg, #2ecc71, #27ae60);
        }

        .stat-card.suppliers::before {
            background: linear-gradient(90deg, #f39c12, #e67e22);
        }

        .stat-card.feedback::before {
            background: linear-gradient(90deg, #9b59b6, #8e44ad);
        }

        .stat-card.revenue::before {
            background: linear-gradient(90deg, #1abc9c, #16a085);
        }

        .stat-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .stat-title {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .stat-number {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .stat-change {
            font-size: 14px;
            color: var(--success-color);
        }

        .stat-link {
            display: inline-block;
            margin-top: 15px;
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .stat-link:hover {
            color: var(--secondary-color);
        }

        .charts-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-container {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .recent-activities {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .activity-icon {
            font-size: 24px;
            margin-right: 15px;
        }

        .activity-content {
            flex: 1;
        }

        .activity-text {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .activity-time {
            font-size: 12px;
            color: #7f8c8d;
        }

        .card {
            margin-left: 20px;
            /* Add space from sidebar */
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 25px;
            margin-bottom: 25px;
        }

        /* Update existing styles to use CSS variables */
        .btn-add {
            display: inline-block;
            padding: 12px 20px;
            background: linear-gradient(135deg, var(--success-color), #27ae60);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        .btn-add:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .styled-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .styled-table th,
        .styled-table td {
            padding: 15px;
            border: none;
            border-bottom: 1px solid var(--border-color);
        }

        .styled-table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        .styled-table tr:last-child td {
            border-bottom: none;
        }

        .styled-table tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .modal-content {
            background-color: var(--card-bg);
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 70%;
            max-width: 800px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
        }

        .modal-content h2 {
            color: var(--primary-color);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-sizing: border-box;
            background-color: var(--card-bg);
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            outline: none;
        }

        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --bg-color: #f4f4f4;
            --card-bg: #ffffff;
            --text-color: #2c3e50;
            --border-color: #eee;
            --sidebar-width: 250px;
        }

        /* Search Section Styles */
        .search-section {
            margin-bottom: 25px;
            padding: 20px;
        }

        .search-fields {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }

        .form-group {
            margin-bottom: 0;
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 30px;
            margin: 30px 0;
        }

        .chart-card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .chart-card canvas {
            width: 100% !important;
            height: 300px !important;
        }

        .chart-card h3 {
            margin-bottom: 15px;
            color: var(--text-color);
        }

        /* Data Table Styles */
        .data-table {
            margin: 20px 0;
            background: var(--card-bg);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        .styled-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 20px 0;
        }

        .styled-table th,
        .styled-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .styled-table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
        }

        .styled-table tr:last-child td {
            border-bottom: none;
        }

        /* Status Colors */
        .status-high {
            color: var(--success-color);
        }

        .status-normal {
            color: var(--accent-color);
        }

        .status-low {
            color: var(--danger-color);
        }

        /* Type Colors */
        .type-new {
            color: var(--accent-color);
        }

        .type-regular {
            color: var(--success-color);
        }

        .type-vip {
            color: var(--warning-color);
        }

        /* Breadcrumb Navigation */
        .breadcrumb {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            background: var(--card-bg);
            border-radius: 8px;
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
        }

        .breadcrumb-item:not(:last-child)::after {
            content: '/';
            margin: 0 10px;
            color: var(--text-color);
            opacity: 0.5;
        }

        .breadcrumb-item a {
            color: var(--accent-color);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: var(--text-color);
        }

        [data-theme="dark"] {
            --primary-color: #1a252f;
            --secondary-color: #2c3e50;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #e67e22;
            --danger-color: #c0392b;
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --text-color: #ffffff;
            --border-color: #333;
        }

        /* تحسينات خاصة بصفحة الطلبات */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
            margin-left: 20px;
            /* Add space from sidebar */
        }

        .dashboard-header h1 {
            color: var(--primary-color);
            font-size: 28px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* تحسين تجربة المستخدم */
        .report-card {
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .report-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        /* تحسينات حالة الطلب */
        .order-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            min-width: 100px;
        }

        .status-pending {
            background-color: #f39c12;
            color: white;
        }

        .status-processing {
            background-color: #3498db;
            color: white;
        }

        .status-shipped {
            background-color: #9b59b6;
            color: white;
        }

        .status-delivered {
            background-color: #2ecc71;
            color: white;
        }

        .status-cancelled {
            background-color: #e74c3c;
            color: white;
        }

        /* تحسينات تنبيهات النجاح */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid transparent;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            border-left-color: #2ecc71;
            color: #27ae60;
        }

        /* تحسينات التصميم المتجاوب */
        @media (max-width: 992px) {

            .stats-grid,
            .report-types {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                overflow-x: hidden;
                white-space: nowrap;
            }

            .sidebar h2 {
                display: none;
            }

            .sidebar a {
                padding: 15px;
                text-align: center;
            }

            .sidebar a span {
                display: none;
            }

            .content {
                margin-left: 70px;
                padding: 15px;
            }

            .theme-toggle-container {
                padding: 10px;
            }

            .btn-theme span {
                display: none;
            }

            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .dashboard-actions {
                margin-top: 15px;
                width: 100%;
            }

            .stats-grid,
            .report-types {
                grid-template-columns: 1fr;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .modal-content {
                width: 95%;
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            .form-actions {
                flex-direction: column;
            }

            .form-actions button {
                width: 100%;
                margin-bottom: 10px;
            }

            .dashboard-header h1 {
                font-size: 1.5rem;
            }
        }

        /* Color Customization Controls */
        .theme-customizer {
            position: fixed;
            right: -300px;
            top: 70px;
            width: 300px;
            background-color: var(--card-bg);
            border-radius: 8px 0 0 8px;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            transition: right 0.3s;
            z-index: 1000;
            padding: 15px;
        }

        .theme-customizer.open {
            right: 0;
        }

        .customizer-toggle {
            position: absolute;
            left: -40px;
            top: 20px;
            background-color: var(--accent-color);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 8px 0 0 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .color-option {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .color-preview {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            margin-right: 10px;
            border: 1px solid var(--border-color);
        }

        /* Button Styles */
        .btn-edit {
            background: linear-gradient(135deg, var(--accent-color), #2980b9);
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-block;
            text-align: center;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-delete,
        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #c0392b);
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-block;
            text-align: center;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--success-color), #27ae60);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-block;
            text-align: center;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            letter-spacing: 0.5px;
        }

        .btn-cancel {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-block;
            text-align: center;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-edit:hover,
        .btn-delete:hover,
        .btn-submit:hover,
        .btn-cancel:hover,
        .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .btn-export,
        .btn-theme {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-pdf {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .btn-excel {
            background: linear-gradient(135deg, #27ae60, #229954);
            color: white;
        }

        .btn-theme {
            background: linear-gradient(135deg, #8e44ad, #9b59b6);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px 20px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin-top: 25px;
            width: 100%;
            letter-spacing: 0.5px;
        }

        .btn-theme:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
            letter-spacing: 1px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin: 30px 0;
            margin-left: 20px;
            /* Add space from sidebar */
        }

        .stat-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-bottom: 0;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-color), var(--success-color));
        }

        .stat-card.products::before {
            background: linear-gradient(90deg, #3498db, #2980b9);
        }

        .stat-card.orders::before {
            background: linear-gradient(90deg, #e74c3c, #c0392b);
        }

        .stat-card.employees::before {
            background: linear-gradient(90deg, #2ecc71, #27ae60);
        }

        .stat-card.suppliers::before {
            background: linear-gradient(90deg, #f39c12, #e67e22);
        }

        .stat-card.feedback::before {
            background: linear-gradient(90deg, #9b59b6, #8e44ad);
        }

        .stat-card.revenue::before {
            background: linear-gradient(90deg, #1abc9c, #16a085);
        }

        .stat-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .stat-title {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .stat-number {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .stat-change {
            font-size: 14px;
            color: var(--success-color);
        }

        .stat-link {
            display: inline-block;
            margin-top: 15px;
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .stat-link:hover {
            color: var(--secondary-color);
        }

        .charts-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-container {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .recent-activities {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .activity-icon {
            font-size: 24px;
            margin-right: 15px;
        }

        .activity-content {
            flex: 1;
        }

        .activity-text {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .activity-time {
            font-size: 12px;
            color: #7f8c8d;
        }

        .card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        /* Update existing styles to use CSS variables */
        .btn-add {
            display: inline-block;
            padding: 12px 20px;
            background: linear-gradient(135deg, var(--success-color), #27ae60);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        .btn-add:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .styled-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .styled-table th,
        .styled-table td {
            padding: 15px;
            border: none;
            border-bottom: 1px solid var(--border-color);
        }

        .styled-table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        .styled-table tr:last-child td {
            border-bottom: none;
        }

        .styled-table tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .modal-content {
            background-color: var(--card-bg);
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 70%;
            max-width: 800px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
        }

        .modal-content h2 {
            color: var(--primary-color);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-sizing: border-box;
            background-color: var(--card-bg);
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            outline: none;
        }

        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --bg-color: #f4f4f4;
            --card-bg: #ffffff;
            --text-color: #2c3e50;
            --border-color: #eee;
            --sidebar-width: 250px;
        }

        /* Search Section Styles */
        .search-section {
            margin-bottom: 25px;
            padding: 20px;
        }

        .search-fields {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }

        .form-group {
            margin-bottom: 0;
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 30px;
            margin: 30px 0;
        }

        .chart-card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .chart-card canvas {
            width: 100% !important;
            height: 300px !important;
        }

        .chart-card h3 {
            margin-bottom: 15px;
            color: var(--text-color);
        }

        /* Data Table Styles */
        .data-table {
            margin: 20px 0;
            background: var(--card-bg);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        .styled-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 20px 0;
        }

        .styled-table th,
        .styled-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .styled-table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
        }

        .styled-table tr:last-child td {
            border-bottom: none;
        }

        /* Status Colors */
        .status-high {
            color: var(--success-color);
        }

        .status-normal {
            color: var(--accent-color);
        }

        .status-low {
            color: var(--danger-color);
        }

        /* Type Colors */
        .type-new {
            color: var(--accent-color);
        }

        .type-regular {
            color: var(--success-color);
        }

        .type-vip {
            color: var(--warning-color);
        }

        /* Breadcrumb Navigation */
        .breadcrumb {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            background: var(--card-bg);
            border-radius: 8px;
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
        }

        .breadcrumb-item:not(:last-child)::after {
            content: '/';
            margin: 0 10px;
            color: var(--text-color);
            opacity: 0.5;
        }

        .breadcrumb-item a {
            color: var(--accent-color);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: var(--text-color);
        }

        [data-theme="dark"] {
            --primary-color: #1a252f;
            --secondary-color: #2c3e50;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #e67e22;
            --danger-color: #c0392b;
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --text-color: #ffffff;
            --border-color: #333;
        }

        /* تحسينات خاصة بصفحة الطلبات */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
            margin-left: 20px;
            /* Add space from sidebar */
        }

        .dashboard-header h1 {
            color: var(--primary-color);
            font-size: 28px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* تحسينات حالة الطلب */
        .order-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            min-width: 100px;
        }

        .status-pending {
            background-color: #f39c12;
            color: white;
        }

        .status-processing {
            background-color: #3498db;
            color: white;
        }

        .status-shipped {
            background-color: #9b59b6;
            color: white;
        }

        .status-delivered {
            background-color: #2ecc71;
            color: white;
        }

        .status-cancelled {
            background-color: #e74c3c;
            color: white;
        }

        /* تحسينات تنبيهات النجاح */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid transparent;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            border-left-color: #2ecc71;
            color: #27ae60;
        }

        /* تحسينات التصميم المتجاوب */
        @media (max-width: 992px) {

            .stats-grid,
            .report-types {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                overflow-x: hidden;
                white-space: nowrap;
            }

            .sidebar h2 {
                display: none;
            }

            .sidebar a {
                padding: 15px;
                text-align: center;
            }

            .sidebar a span {
                display: none;
            }

            .content {
                margin-left: 70px;
                padding: 15px;
            }

            .theme-toggle-container {
                padding: 10px;
            }

            .btn-theme span {
                display: none;
            }

            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .dashboard-actions {
                margin-top: 15px;
                width: 100%;
            }

            .stats-grid,
            .report-types {
                grid-template-columns: 1fr;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .modal-content {
                width: 95%;
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            .form-actions {
                flex-direction: column;
            }

            .form-actions button {
                width: 100%;
                margin-bottom: 10px;
            }

            .dashboard-header h1 {
                font-size: 1.5rem;
            }
        }

        /* Color Customization Controls */
        .theme-customizer {
            position: fixed;
            right: -300px;
            top: 70px;
            width: 300px;
            background-color: var(--card-bg);
            border-radius: 8px 0 0 8px;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            transition: right 0.3s;
            z-index: 1000;
            padding: 15px;
        }

        .theme-customizer.open {
            right: 0;
        }

        .customizer-toggle {
            position: absolute;
            left: -40px;
            top: 20px;
            background-color: var(--accent-color);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 8px 0 0 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .color-option {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .color-preview {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            margin-right: 10px;
            border: 1px solid var(--border-color);
        }

        /* Notification Styles - Consolidated */
        .top-bar {
            position: fixed;
            top: 0;
            right: 0;
            padding: 15px;
            z-index: 1000;
        }

        .notification-dropdown {
            position: relative;
            display: inline-block;
        }

        .notification-btn {
            background-color: var(--card-bg);
            color: var(--text-color);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            position: relative;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: var(--card-bg);
            min-width: 300px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 8px;
            overflow: hidden;
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .notification-header h3 {
            margin: 0;
            font-size: 16px;
        }

        .notification-header a {
            color: var(--accent-color);
            text-decoration: none;
            font-size: 14px;
        }

        .notification-items {
            max-height: 300px;
            overflow-y: auto;
        }

        .notification-item-mini {
            padding: 10px 15px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .notification-item-mini:hover {
            background-color: var(--bg-color);
        }

        .notification-icon-mini {
            font-size: 20px;
            margin-right: 10px;
        }

        .notification-content-mini {
            flex: 1;
        }

        .notification-title-mini {
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .notification-time-mini {
            font-size: 12px;
            color: var(--text-color);
            opacity: 0.6;
        }

        .empty-notification {
            padding: 20px;
            text-align: center;
            color: var(--text-color);
            opacity: 0.7;
        }

        .show {
            display: block;
        }

        /* تحسينات النوافذ المنبثقة */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
            opacity: 0;
        }

        .modal.show {
            display: block;
            opacity: 1;
        }

        .modal-content {
            background-color: var(--card-bg);
            margin: 5vh auto;
            padding: 30px;
            border-radius: 15px;
            width: 70%;
            max-width: 800px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            transform: translateY(-20px);
            transition: all 0.3s ease;
            position: relative;
        }

        .modal.show .modal-content {
            transform: translateY(0);
        }

        .modal-content h2 {
            color: var(--primary-color);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-color);
        }

        .close {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 28px;
            font-weight: bold;
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .close:hover {
            color: var(--danger-color);
            transform: scale(1.1);
        }

        /* تحسين الأزرار في النوافذ المنبثقة */
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }
    </style>

</head>

<body>
    <!-- 🎨 Theme Customizer -->
    <div class="theme-customizer" id="themeCustomizer">
        <div class="customizer-toggle" onclick="toggleCustomizer()">
            <i>🎨</i>
        </div>
        <h3>Theme Settings</h3>

        <!-- Theme Mode -->
        <div class="theme-mode">
            <h4>Mode</h4>
            <div class="form-check">
                <input type="radio" name="theme-mode" id="lightMode" checked onclick="setThemeMode('light')">
                <label for="lightMode">Light Mode</label>
            </div>
            <div class="form-check">
                <input type="radio" name="theme-mode" id="darkMode" onclick="setThemeMode('dark')">
                <label for="darkMode">Dark Mode</label>
            </div>
        </div>

        <!-- Color Settings -->
        <div class="color-customizer">
            <h4>Colors</h4>
            <div class="color-option">
                <div class="color-preview" style="background-color: var(--accent-color);"></div>
                <label>Accent Color</label>
                <input type="color" id="accentColor" value="#3498db" onchange="updateColor('accent-color', this.value)">
            </div>
        </div>

        <button class="btn-submit" onclick="saveThemeSettings()">Save Settings</button>
    </div>

    <!-- 🧭 Sidebar -->
    <div class="sidebar">
        <h2>👑 Admin Dashboard</h2>
        <a href="{{ route('admin.dashboard') }}">📊 Statistics</a>
        <a href="{{ route('admin.products') }}">📦 Products</a>
        <a href="{{ route('admin.categories') }}">🗂️ Categories</a>
        <a href="{{ route('admin.employees') }}">👥 Employees</a>
        <a href="{{ route('admin.suppliers') }}">🏭 Suppliers</a>
        <a href="{{ route('admin.orders') }}">🛒 Orders</a>
        <a href="{{ route('admin.feedback') }}">📝 Feedback</a>
        <a href="{{ route('admin.users') }}">👤 Users</a>
        <a href="{{ route('admin.notifications.index') }}">🔔 Notifications</a>
        <a href="{{ route('admin.activities') }}">🕒 Activities</a>
        <a href="{{ route('admin.reports.index') }}">📈 Reports</a>
        <a href="{{ route('admin.roles.index') }}">🔐 Roles</a>

        <!-- Dark/Light Theme Toggle -->
        <div class="theme-toggle-container">
            <button onclick="toggleTheme()" class="btn-theme" id="themeToggle">🌙 Dark Mode</button>
        </div>
    </div>

    <!-- 🔔 Top Bar Notifications -->
    <div class="top-bar">
        <div class="notification-dropdown">
            <button class="notification-btn" id="notificationBtn">
                🔔 <span class="notification-badge" id="notificationCount">0</span>
            </button>
            <div class="notification-dropdown-content" id="notificationDropdown">
                <div class="notification-header">
                    <h3>Notifications</h3>
                    <a href="{{ route('admin.notifications.index') }}">View All</a>
                </div>
                <div class="notification-items" id="notificationItems">
                    <div class="empty-notification">No new notifications</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 📄 Main Content -->
    <div class="content">
        @yield('content')
    </div>

    @push('scripts')
        <script>
            // ================================
            // 🔔 Notifications Handling
            // ================================
            document.addEventListener('DOMContentLoaded', function () {
                updateNotificationCount();
                setInterval(updateNotificationCount, 30000);

                const notificationBtn = document.getElementById('notificationBtn');
                const notificationDropdown = document.getElementById('notificationDropdown');

                if (notificationBtn) {
                    notificationBtn.addEventListener('click', function () {
                        notificationDropdown.classList.toggle('show');
                        if (notificationDropdown.classList.contains('show')) {
                            loadLatestNotifications();
                        }
                    });
                }

                // Hide dropdown on outside click
                window.addEventListener('click', function (event) {
                    if (!event.target.matches('.notification-btn') &&
                        !event.target.closest('.notification-dropdown-content')) {
                        notificationDropdown?.classList.remove('show');
                    }
                });
            });

            function updateNotificationCount() {
                fetch('{{ route("admin.notifications.unreadCount") }}')
                    .then(response => response.json())
                    .then(data => {
                        const countElement = document.getElementById('notificationCount');
                        if (countElement) {
                            countElement.textContent = data.count;
                            countElement.style.display = (data.count > 0 ? 'flex' : 'none');
                        }
                    })
                    .catch(console.error);
            }

            function loadLatestNotifications() {
                fetch('{{ route("admin.notifications.latest") }}')
                    .then(response => response.json())
                    .then(notifications => {
                        const container = document.getElementById('notificationItems');
                        if (!container) return;

                        if (notifications.length === 0) {
                            container.innerHTML = '<div class="empty-notification">No new notifications</div>';
                            return;
                        }

                        container.innerHTML = '';
                        notifications.forEach(notification => {
                            let icon = '🔔', title = notification.data.title || 'System Notification',
                                link = '{{ route("admin.notifications.index") }}';

                            if (notification.type === 'low_stock') {
                                icon = '📦';
                                title = `Low Stock: ${notification.data.product_title}`;
                                link = '{{ route("admin.products") }}';
                            } else if (notification.type === 'new_order') {
                                icon = '🛒';
                                title = `New Order: #${notification.data.order_id}`;
                                link = '{{ route("admin.orders") }}';
                            }

                            container.innerHTML += `
                            <a href="${link}" class="notification-item-mini ${!notification.read_at ? 'unread' : ''}">
                                <div class="notification-icon-mini">${icon}</div>
                                <div class="notification-content-mini">
                                    <div class="notification-title-mini">${title}</div>
                                    <div class="notification-time-mini">${notification.created_at}</div>
                                </div>
                            </a>`;
                        });
                    })
                    .catch(console.error);
            }

            // ================================
            // 🎨 Theme Functions
            // ================================
            function toggleTheme() {
                document.body.classList.toggle('dark-theme');
                const themeToggle = document.getElementById('themeToggle');
                const isDark = document.body.classList.contains('dark-theme');

                themeToggle.textContent = isDark ? '☀️ Light Mode' : '🌙 Dark Mode';
                localStorage.setItem('theme', isDark ? 'dark' : 'light');

                document.getElementById(isDark ? 'darkMode' : 'lightMode').checked = true;
            }

            document.addEventListener('DOMContentLoaded', function () {
                const savedTheme = localStorage.getItem('theme');
                if (savedTheme === 'dark') {
                    document.body.classList.add('dark-theme');
                    document.getElementById('themeToggle').textContent = '☀️ Light Mode';
                    document.getElementById('darkMode').checked = true;
                }

                const savedAccentColor = localStorage.getItem('accent-color');
                if (savedAccentColor) {
                    document.documentElement.style.setProperty('--accent-color', savedAccentColor);
                    document.getElementById('accentColor').value = savedAccentColor;
                }
            });

            function toggleCustomizer() {
                document.getElementById('themeCustomizer').classList.toggle('open');
            }

            function setThemeMode(mode) {
                if (mode === 'dark' && !document.body.classList.contains('dark-theme')) toggleTheme();
                if (mode === 'light' && document.body.classList.contains('dark-theme')) toggleTheme();
            }

            function updateColor(property, value) {
                document.documentElement.style.setProperty(`--${property}`, value);
            }

            function saveThemeSettings() {
                localStorage.setItem('accent-color', document.getElementById('accentColor').value);
                alert('Theme settings saved!');
            }

            // ================================
            // 📦 Modal Functions
            // ================================
            function openModal(modalId) {
                const modal = document.getElementById(modalId);
                if (!modal) return;

                modal.classList.add('show');
                document.body.style.overflow = 'hidden';

                setTimeout(() => {
                    const firstInput = modal.querySelector('input, select, textarea');
                    if (firstInput) firstInput.focus();
                }, 100);
            }

            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('show');
                    document.body.style.overflow = '';
                }
            }

            document.addEventListener('click', function (event) {
                if (event.target.classList.contains('modal')) closeModal(event.target.id);
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    document.querySelectorAll('.modal.show').forEach(modal => closeModal(modal.id));
                }
            });

            // ================================
            // 🖥️ SPA-Like Navigation
            // ================================
            function loadPage(url, element) {
                document.querySelector('.content').style.opacity = '0.7';

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(response => response.text())
                    .then(html => {
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        const newContent = doc.querySelector('.content');
                        if (newContent) document.querySelector('.content').innerHTML = newContent.innerHTML;

                        history.pushState(null, '', url);
                        document.querySelector('.content').style.opacity = '1';

                        document.querySelectorAll('.sidebar a').forEach(link => link.classList.remove('active'));
                        element.classList.add('active');
                    })
                    .catch(() => window.location.href = url);
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.sidebar a').forEach(link => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();
                        loadPage(this.href, this);
                    });
                });
            });

            window.addEventListener('popstate', function () {
                fetch(window.location.pathname)
                    .then(response => response.text())
                    .then(html => {
                        document.querySelector('.content').innerHTML = html;
                    });
            });
        </script>
    @endpush




</body>

</html>