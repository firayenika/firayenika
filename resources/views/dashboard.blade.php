<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            margin: 0;
            padding: 0;
        }
        nav {
            width: 100%;
            background-color: #4CAF50;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: left;
        }
        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        nav ul li {
            margin: 0 20px;
        }
        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 1.2em;
            transition: color 0.3s;
        }
        nav ul li a:hover {
            color: #d4f7dc;
        }
        h1 {
            margin: 20px;
            color: #4CAF50;
            font-size: 2.5em;
            text-align: center;
        }
        .content {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        table {
            width: 80%;
            background-color: #fff;
            border-collapse: collapse;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        th, td {
            padding: 15px;
            text-align: left;
            transition: background-color 0.3s, color 0.3s, box-shadow 0.3s, transform 0.3s;
        }
        th {
            background-color: #4CAF50;
            color: #fff;
            font-weight: bold;
        }
        td:hover {
            background-color: #e0f7fa;
            color: #00796b;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: scale(1.05);
        }
        tr:nth-child(even) td {
            background-color: #f2f2f2;
        }
        tr:nth-child(odd) td {
            background-color: #fafafa;
        }
        button {
            padding: 10px 20px;
            font-size: 1em;
            color: #fff;
            background-color: #4CAF50;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }
        button:hover {
            background-color: #45a049;
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="#dashboard">Dashboard</a></li>
            <li><a href="#data-table">Data Table</a></li>
        </ul>
    </nav>
    <div class="content">
        <h1 id="dashboard">Monitoring Berat Infus</h1>
        <table>
            <thead>
                <tr>
                    <th>Weight 1</th>
                    <th>Weight 2</th>
                    <th>Percent Weight 1</th>
                    <th>Percent Weight 2</th>
                </tr>
            </thead>
            <tbody id="data-body">
                <tr>
                    <td id="weight1">-</td>
                    <td id="weight2">-</td>
                    <td id="percent_weight1">-</td>
                    <td id="percent_weight2">-</td>
                </tr>
            </tbody>
        </table>
        <button id="reset-button">Reset Data</button>
    </div>
    <script>
        async function fetchData() {
            try {
                const response = await fetch('/monfus/public/api/data');
                const data = await response.json();
                
                document.getElementById('weight1').textContent = data.weight1;
                document.getElementById('weight2').textContent = data.weight2;
                document.getElementById('percent_weight1').textContent = data.percent_weight1;
                document.getElementById('percent_weight2').textContent = data.percent_weight2;
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        function resetData() {
            document.getElementById('weight1').textContent = '-';
            document.getElementById('weight2').textContent = '-';
            document.getElementById('percent_weight1').textContent = '-';
            document.getElementById('percent_weight2').textContent = '-';
        }

        document.getElementById('reset-button').addEventListener('click', resetData);

        setInterval(fetchData, 1000); // Fetch data every second
    </script>
</body>
</html>
