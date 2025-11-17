<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng thành công</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .success-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            text-align: center;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, #EE4D2E 0%, #FF6B4A 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.2s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .success-icon svg {
            width: 48px;
            height: 48px;
            stroke: white;
            stroke-width: 2;
            fill: none;
            animation: checkmark 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.4s both;
        }

        @keyframes checkmark {
            0% {
                stroke-dasharray: 50;
                stroke-dashoffset: 50;
            }
            100% {
                stroke-dasharray: 50;
                stroke-dashoffset: 0;
            }
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #222;
            margin-bottom: 12px;
            animation: fadeIn 0.8s ease-out 0.3s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .subtitle {
            font-size: 15px;
            color: #888;
            margin-bottom: 32px;
            line-height: 1.5;
            animation: fadeIn 0.8s ease-out 0.4s both;
        }

        .info-box {
            background: #f8f8f8;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
            animation: fadeIn 0.8s ease-out 0.5s both;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            font-size: 14px;
            border-bottom: 1px solid #eee;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #888;
            font-weight: 500;
        }

        .info-value {
            color: #222;
            font-weight: 600;
        }

        .button-group {
            display: flex;
            gap: 12px;
            animation: fadeIn 0.8s ease-out 0.6s both;
        }

        .btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #EE4D2E 0%, #FF6B4A 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(238, 77, 46, 0.3);
        }

        .btn-secondary {
            background: white;
            color: #EE4D2E;
            border: 2px solid #EE4D2E;
        }

        .btn-secondary:hover {
            background: #fff5f0;
        }

        @media (max-width: 480px) {
            .success-container {
                padding: 32px 24px;
            }

            h1 {
                font-size: 24px;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <svg viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>

        <h1>Đặt hàng thành công!</h1>
        <p class="subtitle">Cảm ơn bạn đã tin tưởng chúng tôi. Chúng tôi sẽ liên hệ sớm nhất có thể.</p>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Thời gian xử lý:</span>
                <span class="info-value">1-2 ngày làm việc</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kiểm tra tình trạng:</span>
                <span class="info-value">Qua email hoặc điện thoại</span>
            </div>
        </div>

        <div class="button-group">
            <a href="{{ route('home') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
            <a href="{{ route('order.history') }}" class="btn btn-secondary">Xem đơn hàng</a>
        </div>
    </div>
</body>
</html>