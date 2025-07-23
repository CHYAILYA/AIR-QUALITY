<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <style>
        :root {
            --primary: #6366f1;
            --danger: #ef4444;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        body {
            background: linear-gradient(45deg, #f3f4f6, #e5e7eb);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .error-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 90%;
            width: 400px;
        }

        .error-icon {
            font-size: 4rem;
            color: var(--danger);
            margin-bottom: 1rem;
        }

        .error-title {
            color: var(--text-primary);
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .error-message {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.2s ease;
        }

        .back-button:hover {
            opacity: 0.9;
        }
    </style>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="error-container">
        <span class="material-icons error-icon">error_outline</span>
        <h1 class="error-title">Oops! Something went wrong</h1>
        <p class="error-message"><?= $message ?? 'An error occurred while fetching data' ?></p>
        <a href="<?= base_url() ?>" class="back-button">
            <span class="material-icons">arrow_back</span>
            Back to Home
        </a>
    </div>
</body>
</html>