<?php
session_start();

if (isset($_SESSION['user']) && !isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [
        [
            'id' => 1,
            'title' => 'Team Meeting',
            'description' => 'Weekly team sync and project updates',
            'time' => '09:00',
            'date' => date('Y-m-d'),
            'priority' => 'high',
            'completed' => false,
            'category' => 'work'
        ],
        [
            'id' => 2,
            'title' => 'Client Presentation',
            'description' => 'Present quarterly results to key stakeholders',
            'time' => '14:30',
            'date' => date('Y-m-d'),
            'priority' => 'high',
            'completed' => false,
            'category' => 'work'
        ],
        [
            'id' => 3,
            'title' => 'Gym Workout',
            'description' => 'Cardio and strength training session',
            'time' => '18:00',
            'date' => date('Y-m-d'),
            'priority' => 'medium',
            'completed' => false,
            'category' => 'health'
        ],
        [
            'id' => 4,
            'title' => 'Read Chapter 5',
            'description' => 'Continue reading "The Art of Productivity"',
            'time' => '20:00',
            'date' => date('Y-m-d'),
            'priority' => 'low',
            'completed' => true,
            'category' => 'personal'
        ]
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'login':
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            
            if ($username && $password) {
                $_SESSION['user'] = [
                    'username' => $username,
                    'name' => ucfirst($username),
                    'email' => $username . '@example.com',
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($username) . '&background=6366f1&color=fff&size=100'
                ];
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
            }
            exit;
            
        case 'register':
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            
            if ($username && $email && $password) {
                $_SESSION['user'] = [
                    'username' => $username,
                    'name' => ucfirst($username),
                    'email' => $email,
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($username) . '&background=6366f1&color=fff&size=100'
                ];
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
            }
            exit;
            
        case 'logout':
            session_destroy();
            echo json_encode(['success' => true]);
            exit;
            
        case 'add_task':
            if (!isset($_SESSION['tasks'])) $_SESSION['tasks'] = [];
            
            $task = [
                'id' => time() . rand(100, 999), 
                'title' => isset($_POST['title']) ? trim($_POST['title']) : '',
                'description' => isset($_POST['description']) ? trim($_POST['description']) : '',
                'time' => isset($_POST['time']) ? $_POST['time'] : '12:00',
                'date' => isset($_POST['date']) ? $_POST['date'] : date('Y-m-d'),
                'priority' => isset($_POST['priority']) ? $_POST['priority'] : 'medium',
                'completed' => false,
                'category' => isset($_POST['category']) ? $_POST['category'] : 'personal'
            ];
            
            $_SESSION['tasks'][] = $task;
            echo json_encode(['success' => true, 'task' => $task]);
            exit;
            
        case 'toggle_task':
            $taskId = isset($_POST['id']) ? $_POST['id'] : '';
            if (isset($_SESSION['tasks']) && is_array($_SESSION['tasks'])) {
                foreach ($_SESSION['tasks'] as &$task) {
                    if ($task['id'] == $taskId) {
                        $task['completed'] = !$task['completed'];
                        break;
                    }
                }
            }
            echo json_encode(['success' => true]);
            exit;
            
        case 'delete_task':
            $taskId = isset($_POST['id']) ? $_POST['id'] : '';
            if (isset($_SESSION['tasks']) && is_array($_SESSION['tasks'])) {
                $_SESSION['tasks'] = array_filter($_SESSION['tasks'], function($task) use ($taskId) {
                    return $task['id'] != $taskId;
                });
                $_SESSION['tasks'] = array_values($_SESSION['tasks']);
            }
            echo json_encode(['success' => true]);
            exit;
    }
}

$isLoggedIn = isset($_SESSION['user']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Planner Website</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-light: #f5f5f7; /* Very light grey */
            --bg-card: rgba(255, 255, 255, 0.95);
            --text-primary: #1d1d1f; /* Dark text */
            --text-secondary: #666;
            --accent-blue: #007aff; /* Apple blue */
            --accent-green: #34c759;
            --accent-orange: #ff9500;
            --accent-purple: #af52de;
            --border-color: #e5e5ea; /* Light border */
            --shadow-light: 0 1px 3px rgba(0,0,0,0.05), 0 5px 15px rgba(0,0,0,0.03);
            --shadow-medium: 0 3px 6px rgba(0,0,0,0.08), 0 8px 24px rgba(0,0,0,0.06);
            --radius-small: 8px;
            --radius-medium: 12px;
            --radius-large: 18px; /* Increased for cards */
        }

        /* Dark Mode Variables (basic) */
        body.dark-mode {
            --bg-light: #1c1c1e; /* Dark background */
            --bg-card: rgba(28, 28, 30, 0.95);
            --text-primary: #f2f2f7;
            --text-secondary: #a0a0a0;
            --border-color: #38383a;
            --shadow-light: 0 1px 3px rgba(0,0,0,0.2), 0 5px 15px rgba(0,0,0,0.1);
            --shadow-medium: 0 3px 6px rgba(0,0,0,0.3), 0 8px 24px rgba(0,0,0,0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-light);
            min-height: 100vh;
            color: var(--text-primary);
            line-height: 1.6;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .auth-card {
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-large);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: var(--shadow-medium);
            border: 1px solid var(--border-color);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--accent-blue);
            margin-bottom: 10px;
        }

        .auth-header p {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-small);
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--bg-card); /* Consistent background */
            color: var(--text-primary);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.1);
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            border: none;
            border-radius: var(--radius-small);
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            user-select: none; /* Prevent text selection */
        }

        .btn-primary {
            background-color: var(--accent-blue);
            color: white;
            width: 100%;
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        .btn-primary:active {
            transform: translateY(0);
            opacity: 1;
        }

        .btn-secondary {
            background: var(--border-color);
            color: var(--text-primary);
        }

        .btn-secondary:hover {
            background-color: var(--accent-blue);
            color: white;
        }
        .btn-secondary:active {
            transform: translateY(0);
            background-color: var(--accent-blue);
        }
        
        .btn-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .auth-switch {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .auth-switch a {
            color: var(--accent-blue);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-switch a:hover {
            text-decoration: underline;
        }

        .dashboard {
            padding: 20px; /* Overall padding for the dashboard */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            gap: 20px; /* Spacing between header and main content */
        }

        .header {
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-large); /* Rounded corners for the entire header card */
            padding: 20px 30px;
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-color);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-left h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 5px;
        }

        .header-left p {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 15px;
            border-radius: 25px;
            background-color: var(--border-color); /* Matches the border color for subtle background */
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 2px solid var(--accent-blue);
        }

        .user-details h3 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .user-details p {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }
        
        .dark-mode-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 20px;
            transition: background 0.2s ease, color 0.2s ease;
            color: var(--text-primary);
        }

        .dark-mode-toggle:hover {
            background-color: var(--border-color);
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr 300px; /* Task list on left, sidebar on right */
            gap: 20px; /* Spacing between main content and sidebar */
            flex-grow: 1; /* Allows it to take available space */
        }
        
        .add-task-btn-container {
            margin-bottom: 20px; /* Space above task section */
            text-align: center;
        }

        .add-task-btn {
            background-color: var(--accent-blue);
            color: white;
            padding: 15px 30px;
            border-radius: var(--radius-large);
            font-size: 1.1rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: var(--shadow-light);
            transition: all 0.2s ease;
        }

        .add-task-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        .add-task-btn:active {
            transform: translateY(0);
            opacity: 1;
        }

        .tasks-section {
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-large);
            padding: 30px;
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap; /* Allow wrapping on smaller screens */
            gap: 15px;
        }

        .section-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .task-filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 16px;
            border: 1px solid var(--border-color);
            border-radius: 20px;
            background: var(--bg-card);
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: var(--accent-blue);
            color: white;
            border-color: var(--accent-blue);
        }
        .filter-btn:active {
            transform: scale(0.98);
        }

        .task-list {
            flex-grow: 1; /* Allows task list to fill available height */
            overflow-y: auto; /* Enable scrolling for tasks */
            padding-right: 10px; /* Space for scrollbar */
        }
        
        /* Custom Scrollbar */
        .task-list::-webkit-scrollbar {
            width: 8px;
        }
        .task-list::-webkit-scrollbar-track {
            background: var(--border-color);
            border-radius: 10px;
        }
        .task-list::-webkit-scrollbar-thumb {
            background: var(--accent-blue);
            border-radius: 10px;
        }
        .task-list::-webkit-scrollbar-thumb:hover {
            background: var(--accent-blue);
        }

        .task-item {
            background: var(--bg-light); /* Lighter background for items */
            border: 1px solid var(--border-color);
            border-radius: var(--radius-medium);
            padding: 18px 20px; /* Slightly more compact */
            margin-bottom: 12px; /* Smaller margin */
            transition: all 0.2s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 8px; /* Space between internal elements */
        }

        .task-item:hover {
            box-shadow: var(--shadow-light); /* Subtle hover shadow */
            transform: translateY(-2px);
        }

        .task-item.completed {
            opacity: 0.7; /* Slightly less opaque */
            background: rgba(52, 199, 89, 0.1); /* Light green tint */
            border-color: var(--accent-green);
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
        }

        .task-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .task-time {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--accent-blue);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .task-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.4;
            margin-bottom: 5px; /* Adjust spacing */
        }

        .task-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap; /* Allow tags to wrap */
        }

        .task-priority, .task-category-tag {
            padding: 4px 10px;
            border-radius: 15px; /* More rounded */
            font-size: 0.75rem; /* Smaller font */
            font-weight: 600;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .priority-high { background: rgba(255, 59, 48, 0.1); color: #ff3b30; } /* Apple Red */
        .priority-medium { background: rgba(255, 149, 0, 0.1); color: #ff9500; } /* Apple Orange */
        .priority-low { background: rgba(52, 199, 89, 0.1); color: #34c759; } /* Apple Green */
        
        .task-category-tag {
            background: rgba(0, 122, 255, 0.1); /* Light blue for category */
            color: var(--accent-blue);
        }


        .task-actions {
            display: flex;
            gap: 5px;
        }

        .task-action {
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 1rem;
            padding: 6px; /* Slightly more padding */
            border-radius: var(--radius-small);
            transition: all 0.2s ease;
        }

        .task-action:hover {
            background: var(--border-color);
            color: var(--text-primary);
        }
        .task-action:active {
            transform: scale(0.95);
        }

        .task-action.complete {
            color: var(--accent-green);
        }

        .task-action.delete {
            color: #ff3b30; /* Apple Red */
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sidebar-card {
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-large);
            padding: 25px;
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-color);
        }
        
        .sidebar-card h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: var(--border-color);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .progress-fill {
            height: 100%;
            background-color: var(--accent-green);
            transition: width 0.3s ease;
        }

        .progress-text {
            font-size: 0.9rem;
            color: var(--text-secondary);
            text-align: left;
        }
        
        .calendar-widget {
            text-align: left;
        }

        .calendar-date {
            font-size: 3rem;
            font-weight: 700;
            color: var(--accent-blue);
            margin-bottom: 5px;
        }

        .calendar-day {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 5px;
        }

        .calendar-month {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .mini-stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .mini-stat-card {
            background: var(--bg-light);
            border-radius: var(--radius-medium);
            padding: 15px;
            text-align: center;
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-color);
        }

        .mini-stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 1.2rem;
            color: white;
        }

        .mini-stat-icon.blue { background-color: var(--accent-blue); }
        .mini-stat-icon.green { background-color: var(--accent-green); }
        .mini-stat-icon.orange { background-color: var(--accent-orange); }
        .mini-stat-icon.purple { background-color: var(--accent-purple); }

        .mini-stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 3px;
        }

        .mini-stat-label {
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: var(--bg-card);
            backdrop-filter: blur(10px);
            margin: auto;
            padding: 30px;
            border-radius: var(--radius-large);
            width: 90%;
            max-width: 500px;
            box-shadow: var(--shadow-medium);
            border: 1px solid var(--border-color);
            position: relative;
            animation: fadeIn 0.3s ease-out;
        }

        .close-button {
            color: var(--text-secondary);
            position: absolute;
            top: 15px;
            right: 25px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .close-button:hover,
        .close-button:focus {
            color: var(--text-primary);
            text-decoration: none;
        }

        .add-task-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-row {
            display: flex;
            gap: 10px;
        }

        .form-row .form-control {
            flex: 1;
        }

        .form-control select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3E%3Cpath fill='%23666' d='M2 0L0 2h4zm0 5L0 3h4z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 12px;
            padding-right: 30px; /* Make space for the arrow */
        }
        
        @media (max-width: 992px) {
            .main-content {
                grid-template-columns: 1fr; /* Stack on medium screens */
            }
            .sidebar {
                order: -1; /* Sidebar appears first on mobile */
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
                align-items: center;
            }
            
            .header-right {
                flex-direction: column;
                gap: 15px;
            }

            .task-filters {
                justify-content: center;
            }
            
            .form-row {
                flex-direction: column;
            }
            
            .user-info {
                padding: 5px 10px;
            }
            .user-avatar {
                width: 30px;
                height: 30px;
            }
            .dashboard {
                padding: 15px;
                gap: 15px;
            }
            .header, .tasks-section, .sidebar-card, .auth-card, .modal-content {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .mini-stats-grid {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 0 10px;
            }
            
            .header-left h1 {
                font-size: 1.6rem;
            }
            .header-left p {
                font-size: 0.9rem;
            }
            .auth-card {
                padding: 30px 20px;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--accent-blue);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .alert {
            padding: 12px 16px;
            border-radius: var(--radius-small);
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(52, 199, 89, 0.1);
            color: var(--accent-green);
            border: 1px solid var(--accent-green);
        }

        .alert-error {
            background: rgba(255, 59, 48, 0.1);
            color: #ff3b30;
            border: 1px solid #ff3b30;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <?php if (!$isLoggedIn): ?>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1><i class="fas fa-calendar-check"></i> Daily Planner Website</h1>
                <p>Organize your day, achieve your goals</p>
            </div>
            
            <div id="alert-container"></div>
            
            <form id="login-form">
                <div class="form-group">
                    <label for="login-username">Username</label>
                    <input type="text" id="login-username" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-icon">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>
            
            <form id="register-form" class="hidden">
                <div class="form-group">
                    <label for="register-username">Username</label>
                    <input type="text" id="register-username" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="register-email">Email</label>
                    <input type="email" id="register-email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="register-password">Password</label>
                    <input type="password" id="register-password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-icon">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>
            
            <div class="auth-switch">
                <span id="auth-switch-text">Don't have an account?</span>
                <a href="#" id="auth-switch-link">Sign up here</a>
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <div class="dashboard">
        <header class="header fade-in">
            <div class="header-content">
                <div class="header-left">
                    <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</h1>
                    <p>Today is <?php echo date('l, F j, Y'); ?></p>
                </div>
                <div class="header-right">
                    <div class="dark-mode-toggle">
                        <i class="fas fa-moon"></i> Dark
                    </div>
                    <div class="user-info">
                        <img src="<?php echo htmlspecialchars($_SESSION['user']['avatar']); ?>" alt="Avatar" class="user-avatar">
                        <div class="user-details">
                            <h3><?php echo htmlspecialchars($_SESSION['user']['name']); ?></h3>
                            </div>
                    </div>
                    <button class="btn btn-secondary btn-icon" onclick="logout()">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </div>
            </div>
        </header>

        <div class="container main-content-wrapper"> <div class="add-task-btn-container fade-in">
                <button class="btn add-task-btn" onclick="openAddTaskModal()">
                    <i class="fas fa-plus"></i> Add New Task
                </button>
            </div>
            
            <div class="main-content fade-in">
                <div class="tasks-section">
                    <div class="section-header">
                        <h2><i class="fas fa-list-ul"></i> Today's Tasks</h2>
                        <div class="task-filters">
                            <button class="filter-btn active" data-filter="all">All</button>
                            <button class="filter-btn" data-filter="pending">Pending</button>
                            <button class="filter-btn" data-filter="completed">Completed</button>
                            <button class="filter-btn" data-filter="high">High Priority</button>
                        </div>
                    </div>
                    
                    <div class="task-list" id="task-list">
                        <?php if (!isset($_SESSION['tasks']) || empty($_SESSION['tasks'])): ?>
                        <div class="text-center" style="padding: 40px; color: var(--text-secondary);">
                            <i class="fas fa-clipboard-list" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.3;"></i>
                            <p>No tasks yet. Add your first task to get started!</p>
                        </div>
                        <?php else: ?>
                        <?php foreach ($_SESSION['tasks'] as $task): ?>
                        <div class="task-item <?php echo (isset($task['completed']) && $task['completed']) ? 'completed' : ''; ?>" 
                             data-priority="<?php echo htmlspecialchars($task['priority'] ?? 'medium'); ?>" 
                             data-status="<?php echo (isset($task['completed']) && $task['completed']) ? 'completed' : 'pending'; ?>">
                            <div class="task-header">
                                <div>
                                    <div class="task-title"><?php echo htmlspecialchars($task['title'] ?? 'Untitled Task'); ?></div>
                                    <div class="task-time">
                                        <i class="fas fa-clock"></i>
                                        <?php echo isset($task['time']) ? date('g:i A', strtotime($task['time'])) : '12:00 PM'; ?>
                                    </div>
                                </div>
                                <div class="task-actions">
                                    <button class="task-action complete" onclick="toggleTask('<?php echo $task['id']; ?>')" title="Toggle Complete">
                                        <i class="fas fa-<?php echo (isset($task['completed']) && $task['completed']) ? 'undo' : 'check'; ?>"></i>
                                    </button>
                                    <button class="task-action delete" onclick="deleteTask('<?php echo $task['id']; ?>')" title="Delete Task">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="task-description"><?php echo htmlspecialchars($task['description'] ?? ''); ?></div>
                            <div class="task-meta">
                                <span class="task-priority priority-<?php echo htmlspecialchars($task['priority'] ?? 'medium'); ?>">
                                    <?php echo ucfirst($task['priority'] ?? 'Medium'); ?> Priority
                                </span>
                                <span class="task-category-tag">
                                    <?php echo ucfirst($task['category'] ?? 'Personal'); ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="sidebar">
                    <div class="sidebar-card">
                        <h3><i class="fas fa-chart-pie"></i> Progress Today</h3>
                        <div class="progress-bar">
                            <div class="progress-fill" id="progress-fill" style="width: <?php 
                                $total = isset($_SESSION['tasks']) ? count($_SESSION['tasks']) : 0;
                                $completed = 0;
                                if (isset($_SESSION['tasks']) && is_array($_SESSION['tasks'])) {
                                    $completed = count(array_filter($_SESSION['tasks'], function($task) { 
                                        return isset($task['completed']) && $task['completed']; 
                                    }));
                                }
                                echo $total > 0 ? round(($completed / $total) * 100) : 0;
                            ?>%"></div>
                        </div>
                        <div class="progress-text">
                            <strong id="progress-completed"><?php echo $completed; ?></strong> of 
                            <strong id="progress-total"><?php echo $total; ?></strong> tasks completed
                        </div>
                    </div>
                    
                    <div class="sidebar-card">
                        <h3><i class="fas fa-calendar-alt"></i> Today</h3>
                        <div class="calendar-widget">
                            <div class="calendar-date"><?php echo date('d'); ?></div>
                            <div class="calendar-day"><?php echo date('l'); ?></div>
                            <div class="calendar-month"><?php echo date('F Y'); ?></div>
                        </div>
                    </div>

                    <div class="mini-stats-grid">
                        <div class="mini-stat-card">
                            <div class="mini-stat-icon blue">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="mini-stat-value" id="total-tasks">
                                <?php echo isset($_SESSION['tasks']) ? count($_SESSION['tasks']) : 0; ?>
                            </div>
                            <div class="mini-stat-label">Total Tasks</div>
                        </div>
                        <div class="mini-stat-card">
                            <div class="mini-stat-icon green">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="mini-stat-value" id="completed-tasks">
                                <?php 
                                $completed = 0;
                                if (isset($_SESSION['tasks']) && is_array($_SESSION['tasks'])) {
                                    $completed = count(array_filter($_SESSION['tasks'], function($task) { 
                                        return isset($task['completed']) && $task['completed']; 
                                    }));
                                }
                                echo $completed;
                                ?>
                            </div>
                            <div class="mini-stat-label">Completed</div>
                        </div>
                        <div class="mini-stat-card">
                            <div class="mini-stat-icon orange">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="mini-stat-value" id="pending-tasks">
                                <?php 
                                $pending = 0;
                                if (isset($_SESSION['tasks']) && is_array($_SESSION['tasks'])) {
                                    $pending = count(array_filter($_SESSION['tasks'], function($task) { 
                                        return !isset($task['completed']) || !$task['completed']; 
                                    }));
                                }
                                echo $pending;
                                ?>
                            </div>
                            <div class="mini-stat-label">Pending</div>
                        </div>
                        <div class="mini-stat-card">
                            <div class="mini-stat-icon purple">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="mini-stat-value" id="productivity-score">
                                <?php 
                                $total = isset($_SESSION['tasks']) ? count($_SESSION['tasks']) : 0;
                                $completed = 0;
                                if (isset($_SESSION['tasks']) && is_array($_SESSION['tasks'])) {
                                    $completed = count(array_filter($_SESSION['tasks'], function($task) { 
                                        return isset($task['completed']) && $task['completed']; 
                                    }));
                                }
                                echo $total > 0 ? round(($completed / $total) * 100) : 0;
                                ?>%
                            </div>
                            <div class="mini-stat-label">Productivity</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="addTaskModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeAddTaskModal()">&times;</span>
            <h3><i class="fas fa-plus-circle"></i> Add New Task</h3>
            <form class="add-task-form" id="add-task-form">
                <div class="form-group">
                    <input type="text" name="title" placeholder="Task title..." class="form-control" required>
                </div>
                <div class="form-group">
                    <textarea name="description" placeholder="Description..." class="form-control" rows="3"></textarea>
                </div>
                <div class="form-row">
                    <input type="time" name="time" class="form-control" value="<?php echo date('H:i'); ?>" required>
                    <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-row">
                    <select name="priority" class="form-control" required>
                        <option value="low">Low Priority</option>
                        <option value="medium" selected>Medium Priority</option>
                        <option value="high">High Priority</option>
                    </select>
                    <select name="category" class="form-control" required>
                        <option value="work">Work</option>
                        <option value="personal" selected>Personal</option>
                        <option value="health">Health</option>
                        <option value="learning">Learning</option>
                        <option value="shopping">Shopping</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i> Add Task
                </button>
            </form>
        </div>
    </div>

    <?php endif; ?>

    <script>
        let currentFilter = 'all';
        let isLoginForm = true;
        function showAlert(message, type = 'error') {
            const container = document.getElementById('alert-container');
            if (!container) { // If not on auth page, use a generic alert
                alert(message);
                return;
            }
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
            container.innerHTML = '';
            container.appendChild(alert);
            
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }

        function toggleAuthForm() {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const switchText = document.getElementById('auth-switch-text');
            const switchLink = document.getElementById('auth-switch-link');

            if (isLoginForm) {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                switchText.textContent = 'Already have an account?';
                switchLink.textContent = 'Sign in here'; // Corrected text
                isLoginForm = false;
            } else {
                registerForm.classList.add('hidden');
                loginForm.classList.remove('hidden');
                switchText.textContent = "Don't have an account?";
                switchLink.textContent = 'Sign up here'; // Corrected text
                isLoginForm = true;
            }
        }

        function submitAuth(form, action) {
            const formData = new FormData(form);
            formData.append('action', action);
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner"></span> Processing...';
            submitBtn.disabled = true;

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Welcome! Redirecting...', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(data.message || 'Authentication failed. Please try again.');
                }
            })
            .catch(error => {
                showAlert('Network error. Please try again.');
                console.error('Error:', error);
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                const formData = new FormData();
                formData.append('action', 'logout');
                
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        }

        function addTask(form) {
            const formData = new FormData(form);
            formData.append('action', 'add_task');
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner"></span> Adding...';
            submitBtn.disabled = true;

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); 
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Failed to add task. Please try again.');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        function toggleTask(taskId) {
            const formData = new FormData();
            formData.append('action', 'toggle_task');
            formData.append('id', taskId);

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); 
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Failed to update task. Please try again.');
            });
        }

        function deleteTask(taskId) {
            if (confirm('Are you sure you want to delete this task?')) {
                const formData = new FormData();
                formData.append('action', 'delete_task');
                formData.append('id', taskId);

                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); 
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Failed to delete task. Please try again.');
                });
            }
        }

        function filterTasks(filter) {
            currentFilter = filter;
            const tasks = document.querySelectorAll('.task-item');
            const filterButtons = document.querySelectorAll('.filter-btn');
            
            filterButtons.forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.filter === filter) {
                    btn.classList.add('active');
                }
            });
            
            tasks.forEach(task => {
                let show = false;
                
                switch (filter) {
                    case 'all':
                        show = true;
                        break;
                    case 'pending':
                        show = task.dataset.status === 'pending';
                        break;
                    case 'completed':
                        show = task.dataset.status === 'completed';
                        break;
                    case 'high':
                        show = task.dataset.priority === 'high';
                        break;
                }
                
                task.style.display = show ? 'flex' : 'none'; // Use flex for task item
            });
        }

        // Modal functions
        function openAddTaskModal() {
            document.getElementById('addTaskModal').style.display = 'flex';
        }

        function closeAddTaskModal() {
            document.getElementById('addTaskModal').style.display = 'none';
        }

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const isDarkMode = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDarkMode);
            updateDarkModeIcon(isDarkMode);
        }

        function updateDarkModeIcon(isDarkMode) {
            const icon = document.querySelector('.dark-mode-toggle i');
            if (icon) {
                icon.className = isDarkMode ? 'fas fa-sun' : 'fas fa-moon';
            }
        }


        document.addEventListener('DOMContentLoaded', function() {
            const authSwitchLink = document.getElementById('auth-switch-link');
            if (authSwitchLink) {
                authSwitchLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleAuthForm();
                });
            }

            const loginForm = document.getElementById('login-form');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    submitAuth(this, 'login');
                });
            }

            const registerForm = document.getElementById('register-form');
            if (registerForm) {
                registerForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    submitAuth(this, 'register');
                });
            }

            const addTaskForm = document.getElementById('add-task-form');
            if (addTaskForm) {
                addTaskForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (validateTaskForm(this)) {
                        addTask(this);
                        closeAddTaskModal();
                    }
                });
            }

            const filterButtons = document.querySelectorAll('.filter-btn');
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    filterTasks(this.dataset.filter);
                });
            });

            const elementsToAnimate = document.querySelectorAll('.fade-in');
            elementsToAnimate.forEach((element, index) => {
                setTimeout(() => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 100);
            });

            const darkModeToggle = document.querySelector('.dark-mode-toggle');
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', toggleDarkMode);
                const savedDarkMode = localStorage.getItem('darkMode');
                if (savedDarkMode === 'true') {
                    document.body.classList.add('dark-mode');
                }
                updateDarkModeIcon(savedDarkMode === 'true');
            }

            setInterval(updateTime, 60000);
        });

        function updateTime() {
            const now = new Date();
            const timeElements = document.querySelectorAll('[data-time]');
            timeElements.forEach(element => {
                const taskTime = element.dataset.time;
            });
        }

        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'n' && document.getElementById('addTaskModal')) {
                e.preventDefault();
                openAddTaskModal();
                const titleInput = document.querySelector('#addTaskModal input[name="title"]');
                if (titleInput) titleInput.focus();
            }
            
            if (e.key === 'Escape' && document.getElementById('addTaskModal').style.display === 'flex') {
                closeAddTaskModal();
            } else if (e.key === 'Escape' && currentFilter !== 'all') {
                filterTasks('all');
            }
        });

        function validateTaskForm(form) {
            const title = form.querySelector('[name="title"]').value.trim();
            const time = form.querySelector('[name="time"]').value;
            const date = form.querySelector('[name="date"]').value;
            
            if (!title) {
                showAlert('Please enter a task title');
                return false;
            }
            
            if (title.length > 100) {
                showAlert('Task title is too long (max 100 characters)');
                return false;
            }
            
            const selectedDate = new Date(date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                if (!confirm('You are scheduling a task for a past date. Continue?')) {
                    return false;
                }
            }
            
            return true;
        }

        let autoSaveTimer;
        function triggerAutoSave() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                // In a real application, this would send an AJAX request to save form state
                console.log('Auto-save triggered for form data.');
            }, 2000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const addTaskForm = document.getElementById('add-task-form');
            if (addTaskForm) {
                addTaskForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (validateTaskForm(this)) {
                        addTask(this);
                    }
                });
                
                const inputs = addTaskForm.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    input.addEventListener('input', triggerAutoSave);
                });
            }
        });
    </script>
</body>
</html>