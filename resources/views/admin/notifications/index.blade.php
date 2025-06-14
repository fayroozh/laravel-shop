@extends('layouts.admin')

@section('content')
    <div class="dashboard-header">
        <h1>🔔 Notifications</h1>
        <div class="dashboard-actions">
            <form action="{{ route('admin.notifications.markAllAsRead') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn-action">Mark All as Read</button>
            </form>
        </div>
    </div>

    <div class="notifications-container">
        <div class="notification-tabs">
            <button class="tab-btn active" onclick="showTab('all')">All</button>
            <button class="tab-btn" onclick="showTab('unread')">Unread</button>
            <button class="tab-btn" onclick="showTab('inventory')">Inventory</button>
            <button class="tab-btn" onclick="showTab('orders')">Orders</button>
            <button class="tab-btn" onclick="showTab('system')">System</button>
        </div>

        <div class="notification-list" id="all-tab">
            @if(count($notifications) > 0)
                @foreach($notifications as $notification)
                    <div class="notification-item {{ $notification->read_at ? '' : 'unread' }}" data-type="{{ $notification->data['type'] ?? 'system' }}">
                        <div class="notification-icon">
                            @if(($notification->data['type'] ?? '') == 'low_stock')
                                📦
                            @elseif(($notification->data['type'] ?? '') == 'new_order')
                                🛒
                            @else
                                🔔
                            @endif
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">
                                @if(($notification->data['type'] ?? '') == 'low_stock')
                                    Low Stock Alert: {{ $notification->data['product_title'] }}
                                @elseif(($notification->data['type'] ?? '') == 'new_order')
                                    New Order: #{{ $notification->data['order_id'] }}
                                @else
                                    {{ $notification->data['title'] ?? 'System Notification' }}
                                @endif
                            </div>
                            <div class="notification-message">
                                @if(($notification->data['type'] ?? '') == 'low_stock')
                                    Current stock: {{ $notification->data['current_stock'] }} (Min: {{ $notification->data['min_stock'] }})
                                @elseif(($notification->data['type'] ?? '') == 'new_order')
                                    {{ $notification->data['message'] ?? 'A new order has been placed.' }}
                                @else
                                    {{ $notification->data['message'] ?? 'You have a new notification.' }}
                                @endif
                            </div>
                            <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="notification-actions">
                            @if(!$notification->read_at)
                                <form action="{{ route('admin.notifications.markAsRead', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-mark-read">Mark as Read</button>
                                </form>
                            @endif
                            
                            @if(($notification->data['type'] ?? '') == 'low_stock')
                                <a href="{{ route('admin.products') }}" class="btn-view">View Product</a>
                            @elseif(($notification->data['type'] ?? '') == 'new_order')
                                <a href="{{ route('admin.orders') }}" class="btn-view">View Order</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <div class="empty-icon">🔔</div>
                    <div class="empty-message">No notifications to display</div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Add active class to clicked button
            event.target.classList.add('active');
            
            // Show all notifications first
            document.querySelectorAll('.notification-item').forEach(item => {
                item.style.display = 'flex';
            });
            
            // Filter based on tab
            if (tabName !== 'all') {
                if (tabName === 'unread') {
                    document.querySelectorAll('.notification-item:not(.unread)').forEach(item => {
                        item.style.display = 'none';
                    });
                } else {
                    document.querySelectorAll(`.notification-item:not([data-type="${tabName}"])`).forEach(item => {
                        item.style.display = 'none';
                    });
                }
            }
        }
    </script>

    <style>
        .notifications-container {
            background-color: var(--card-bg);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .notification-tabs {
            display: flex;
            background-color: var(--bg-color);
            border-bottom: 1px solid var(--border-color);
        }
        
        .tab-btn {
            padding: 15px 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 500;
            color: var(--text-color);
            opacity: 0.7;
            transition: all 0.3s ease;
        }
        
        .tab-btn.active {
            opacity: 1;
            border-bottom: 2px solid var(--accent-color);
        }
        
        .notification-list {
            padding: 0;
        }
        
        .notification-item {
            display: flex;
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
            background-color: var(--card-bg);
        }
        
        .notification-item.unread {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .notification-icon {
            font-size: 24px;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: var(--bg-color);
            border-radius: 50%;
        }
        
        .notification-content {
            flex: 1;
        }
        
        .notification-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: var(--text-color);
        }
        
        .notification-message {
            color: var(--text-color);
            opacity: 0.8;
            margin-bottom: 5px;
        }
        
        .notification-time {
            font-size: 12px;
            color: var(--text-color);
            opacity: 0.6;
        }
        
        .notification-actions {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-mark-read {
            padding: 5px 10px;
            background-color: var(--bg-color);
            border: 1px solid var(--border-color);
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        
        .btn-mark-read:hover {
            background-color: var(--accent-color);
            color: white;
            border-color: var(--accent-color);
        }
        
        .btn-view {
            padding: 5px 10px;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .btn-view:hover {
            background-color: var(--primary-color);
        }
        
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px 20px;
            text-align: center;
        }
        
        .empty-icon {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-message {
            font-size: 18px;
            color: var(--text-color);
            opacity: 0.7;
        }
        
        .btn-action {
            padding: 8px 16px;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-action:hover {
            background-color: var(--primary-color);
        }
    </style>
@endsection