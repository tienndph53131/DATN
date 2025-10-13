<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StyleHub Admin - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* <CHANGE> Updated color scheme from dark to white/light theme */
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --bg-card: #ffffff;
            --border-color: #e5e7eb;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --accent-blue: #3b82f6;
            --accent-blue-dark: #2563eb;
            --accent-green: #10b981;
            --accent-orange: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background-color: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            padding: 1.5rem 0;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 0 1.5rem 2rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sidebar-brand i {
            color: var(--accent-blue);
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
        }

        .nav-section {
            padding: 1rem 1.5rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-secondary);
            letter-spacing: 0.05em;
        }

        .nav-item {
            margin: 0.25rem 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s;
            gap: 0.75rem;
            font-size: 0.95rem;
        }

        .nav-link:hover {
            background-color: var(--bg-card);
            color: var(--text-primary);
        }

        .nav-link.active {
            background-color: var(--bg-card);
            color: var(--accent-blue);
            border-left: 3px solid var(--accent-blue);
        }

        .nav-link i {
            font-size: 1.1rem;
            width: 20px;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 2rem;
            min-height: 100vh;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .header-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .time-filter {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-blue-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.5rem;
            transition: transform 0.2s;
            /* <CHANGE> Added subtle shadow for depth on white background */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            /* <CHANGE> Enhanced shadow on hover */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-icon.blue {
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--accent-blue);
        }

        .stat-icon.green {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--accent-green);
        }

        .stat-icon.orange {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--accent-orange);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .stat-change {
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .stat-change.positive {
            color: var(--accent-green);
        }

        .stat-change.negative {
            color: #ef4444;
        }

        /* Chart Card */
        .chart-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            /* <CHANGE> Added subtle shadow for depth */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chart-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .chart-legend {
            display: flex;
            gap: 1.5rem;
            font-size: 0.875rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .chart-container {
            height: 300px;
            background: linear-gradient(180deg, rgba(59, 130, 246, 0.1) 0%, transparent 100%);
            border-radius: 0.5rem;
            position: relative;
            overflow: hidden;
        }

        .chart-line {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 70%;
            background: linear-gradient(to right, 
                transparent 0%, 
                rgba(59, 130, 246, 0.3) 20%, 
                rgba(59, 130, 246, 0.5) 40%, 
                rgba(59, 130, 246, 0.4) 60%, 
                rgba(59, 130, 246, 0.6) 80%, 
                rgba(59, 130, 246, 0.3) 100%
            );
            clip-path: polygon(
                0% 60%, 5% 55%, 10% 50%, 15% 48%, 20% 45%, 
                25% 50%, 30% 52%, 35% 48%, 40% 45%, 45% 42%, 
                50% 40%, 55% 38%, 60% 42%, 65% 45%, 70% 43%, 
                75% 40%, 80% 38%, 85% 35%, 90% 32%, 95% 30%, 
                100% 28%, 100% 100%, 0% 100%
            );
        }

        /* Table */
        .table-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.5rem;
            overflow-x: auto;
            /* <CHANGE> Added subtle shadow for depth */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .table-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .search-box {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            width: 250px;
        }

        .search-box::placeholder {
            color: var(--text-secondary);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            border-bottom: 1px solid var(--border-color);
        }

        .data-table th {
            padding: 1rem;
            text-align: left;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .data-table tbody tr:hover {
            background-color: var(--bg-secondary);
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .product-image {
            width: 50px;
            height: 50px;
            border-radius: 0.5rem;
            background-color: var(--bg-secondary);
            object-fit: cover;
        }

        .product-details {
            display: flex;
            flex-direction: column;
        }

        .product-name {
            font-weight: 600;
            color: var(--text-primary);
        }

        .product-category {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .badge {
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-block;
        }

        .badge-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--accent-green);
        }

        .badge-warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--accent-orange);
        }

        .badge-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .btn-action {
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0.5rem;
            transition: color 0.2s;
        }

        .btn-action:hover {
            color: var(--accent-blue);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-shop"></i>
            StyleHub
        </div>
        
        <ul class="sidebar-nav">
            <li class="nav-section">Main</li>
            <li class="nav-item">
                <a href="#" class="nav-link active">
                    <i class="bi bi-grid"></i>
                    <span>Overview</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-graph-up"></i>
                    <span>Analytics</span>
                </a>
            </li>
            
            <li class="nav-section">Commerce</li>
            <li class="nav-item">
                <a href="{{ route('products.index') }}" class="nav-link">
                    <i class="bi bi-box-seam"></i>
                    <span>Products</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-cart3"></i>
                    <span>Orders</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-people"></i>
                    <span>Customers</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('categories.index') }}" class="nav-link">
                    <i class="bi bi-tag"></i>
                    <span>Categories</span>
                </a>
            </li>
            
            <li class="nav-section">Management</li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-truck"></i>
                    <span>Inventory</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-credit-card"></i>
                    <span>Payments</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-megaphone"></i>
                    <span>Marketing</span>
                </a>
            </li>
            
            <li class="nav-section">Settings</li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-bell"></i>
                    <span>Notifications</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        
        @include('layouts.admin.header')

        <!-- Stats Grid -->
        @yield('content')

        <!-- Recent Orders Table -->
        <div class="table-card">
            <div class="table-header">
                <h2 class="table-title">Recent Orders</h2>
                <input type="text" class="search-box" placeholder="Search orders...">
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#ORD-2847</td>
                        <td>
                            <div class="product-info">
                                <img src="/placeholder.svg?height=50&width=50" alt="Product" class="product-image">
                                <div class="product-details">
                                    <span class="product-name">Premium Leather Jacket</span>
                                    <span class="product-category">Outerwear</span>
                                </div>
                            </div>
                        </td>
                        <td>Sarah Johnson</td>
                        <td>Oct 10, 2025</td>
                        <td>$299.00</td>
                        <td><span class="badge badge-success">Delivered</span></td>
                        <td>
                            <button class="btn-action"><i class="bi bi-eye"></i></button>
                            <button class="btn-action"><i class="bi bi-three-dots-vertical"></i></button>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>