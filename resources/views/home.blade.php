<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit Margin Manager</title>
    <style>
        .box {
            background: #d2f1df;
            border-radius: 5px;
            box-shadow: 6px 6px 18px 0px #85b79f;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        input[type="number"] {
            width: 80px;
        }

        .btn {
            margin-top: 15px;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
            margin-right: 10px;
        }

        .alert {
            margin-top: 10px;
            padding: 10px;
            background-color: #cce5ff;
        }

    </style>
</head>

<body>

    <div class="box">
        <h2>Profit Margin Table</h2>

        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('profit.table') }}">
            <label for="product">Select Product:</label>
            <select id="product" name="product_id" onchange="this.form.submit()">
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-cost="{{ $product->making_cost }}"
                        data-price="{{ $product->selling_price }}"
                        {{ $product->id == $selectedProductId ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </form>

        @php
            $selectedProduct = $products->firstWhere('id', $selectedProductId);
        @endphp

        @if($selectedProduct)
            <div style="margin-top:10px;">
                <strong>Making Cost:</strong> ₹{{ number_format($selectedProduct->making_cost, 2) }} &nbsp;
                <strong>Selling Price:</strong> ₹{{ number_format($selectedProduct->selling_price, 2) }} &nbsp;
                <strong>Profit:</strong>
                ₹{{ number_format($selectedProduct->selling_price - $selectedProduct->making_cost, 2) }}
            </div>

            <form method="POST" action="{{ route('save.margins') }}">
                @csrf

                <input type="hidden" name="product_id" id="product_id" value="{{ $selectedProductId }}">
                <input type="hidden" id="cost" value="{{ $selectedProduct->making_cost }}">
                <input type="hidden" id="price" value="{{ $selectedProduct->selling_price }}">

                <table>
                    <thead>
                        <tr>
                            <th>Quantity Range</th>
                            <th>Company %</th>
                            <th>Distributor %</th>
                            <th>Shop %</th>
                            <th>Admin % (Auto)</th>
                            <th>Company ₹</th>
                            <th>Distributor ₹</th>
                            <th>Shop ₹</th>
                            <th>Admin ₹</th>
                        </tr>
                    </thead>
                    <tbody id="marginTableBody">
                        @foreach($ranges as $i => $range)
                            <tr>
                                <input type="hidden" name="ranges[{{ $i }}][id]"
                                    value="{{ $range['id'] ?? '' }}">
                                <td>
                                    <input type="number" name="ranges[{{ $i }}][min]"
                                        value="{{ $range['min'] }}" placeholder="Min">
                                    -
                                    <input type="number" name="ranges[{{ $i }}][max]"
                                        value="{{ $range['max'] }}" placeholder="Max">
                                </td>
                                <td><input type="number" name="ranges[{{ $i }}][company]" class="margin-input"
                                        data-role="company" value="{{ $range['company'] }}">
                                </td>
                                <td><input type="number" name="ranges[{{ $i }}][distributor]" class="margin-input"
                                        data-role="distributor"
                                        value="{{ $range['distributor'] }}"></td>
                                <td><input type="number" name="ranges[{{ $i }}][shop]" class="margin-input"
                                        data-role="shop" value="{{ $range['shop'] }}"></td>
                                <td id="admin_percent_{{ $i }}">0%</td>
                                <td id="company_value_{{ $i }}">₹0</td>
                                <td id="distributor_value_{{ $i }}">₹0</td>
                                <td id="shop_value_{{ $i }}">₹0</td>
                                <td id="admin_value_{{ $i }}">₹0</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="button" onclick="addRow()" class="btn btn-success">➕ Add Range</button>
                <button type="submit" class="btn btn-primary">Save Margins</button>
            </form>
        @else
            <div class="alert">No product selected.</div>
        @endif
    </div>

    <script>
        function calculateAll() {
            const cost = parseFloat(document.getElementById('cost').value) || 0;
            const price = parseFloat(document.getElementById('price').value) || 0;
            const profit = price - cost;

            const rows = document.querySelectorAll('#marginTableBody tr');
            rows.forEach((row, index) => {
                const company = parseFloat(row.querySelector('[data-role="company"]').value) || 0;
                const distributor = parseFloat(row.querySelector('[data-role="distributor"]').value) || 0;
                const shop = parseFloat(row.querySelector('[data-role="shop"]').value) || 0;

                const admin = 100 - (company + distributor + shop);

                document.getElementById(`admin_percent_${index}`).innerText = admin.toFixed(2) + '%';
                document.getElementById(`company_value_${index}`).innerText = '₹' + ((profit * company) / 100)
                    .toFixed(2);
                document.getElementById(`distributor_value_${index}`).innerText = '₹' + ((profit *
                    distributor) / 100).toFixed(2);
                document.getElementById(`shop_value_${index}`).innerText = '₹' + ((profit * shop) / 100)
                    .toFixed(2);
                document.getElementById(`admin_value_${index}`).innerText = '₹' + ((profit * admin) / 100)
                    .toFixed(2);
            });
        }

        function addRow() {
            const tbody = document.getElementById('marginTableBody');
            const index = tbody.children.length;

            const row = document.createElement('tr');
            row.innerHTML = `
            <td>
                <input type="number" name="ranges[${index}][min]" placeholder="Min">
                -
                <input type="number" name="ranges[${index}][max]" placeholder="Max">
            </td>
            <td><input type="number" name="ranges[${index}][company]" class="margin-input" data-role="company" value="0"></td>
            <td><input type="number" name="ranges[${index}][distributor]" class="margin-input" data-role="distributor" value="0"></td>
            <td><input type="number" name="ranges[${index}][shop]" class="margin-input" data-role="shop" value="0"></td>
            <td id="admin_percent_${index}">0%</td>
            <td id="company_value_${index}">₹0</td>
            <td id="distributor_value_${index}">₹0</td>
            <td id="shop_value_${index}">₹0</td>
            <td id="admin_value_${index}">₹0</td>
        `;
            tbody.appendChild(row);

            row.querySelectorAll('.margin-input').forEach(input => {
                input.addEventListener('input', calculateAll);
            });

            calculateAll();
        }

        document.querySelectorAll('.margin-input').forEach(input => {
            input.addEventListener('input', calculateAll);
        });

        window.addEventListener('load', calculateAll);

    </script>

</body>

</html>
