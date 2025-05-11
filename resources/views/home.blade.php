<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
<div>
    <h2>Profit Margin Table</h2>

    @if(session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('profit.table') }}">
        <label for="product" >Select Product:</label>
        <select id="product" name="product_id" onchange="this.form.submit()">
            @foreach($products as $product)
                <option value="{{ $product->id }}"
                    data-cost="{{ $product->making_cost }}"
                    data-price="{{ $product->selling_price }}"
                    {{ $product->id == $selectedProductId ? 'selected' : '' }}>
                    {{ $product->name }}
                </option>
            @endforeach
        </select>
        
        @php
    $selectedProduct = $products->firstWhere('id', $selectedProductId);
@endphp

@if($selectedProduct)
    <div>
        <strong>Making Cost:</strong> ₹{{ number_format($selectedProduct->making_cost, 2) }} &nbsp;
        <strong>Selling Price:</strong> ₹{{ number_format($selectedProduct->selling_price, 2) }} &nbsp;
        <strong>Profit:</strong> ₹{{ number_format($selectedProduct->selling_price - $selectedProduct->making_cost, 2) }}
    </div>
@endif
    </form>

    @php
        $selectedProduct = $products->firstWhere('id', $selectedProductId);
    @endphp

    @if($selectedProduct && count($ranges))
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
                <tbody>
                    @foreach($ranges as $i => $range)
                        <tr>
                            <td>
                                {{ $range['min'] }} - {{ $range['max'] }}
                                <input type="hidden" name="ranges[{{ $i }}][min]" value="{{ $range['min'] }}">
                                <input type="hidden" name="ranges[{{ $i }}][max]" value="{{ $range['max'] }}">
                            </td>
                            <td>
                                <input type="number" name="ranges[{{ $i }}][company]" class="form-control margin-input" data-row="{{ $i }}" data-role="company" value="{{ $range['company'] }}">
                            </td>
                            <td>
                                <input type="number" name="ranges[{{ $i }}][distributor]" class="form-control margin-input" data-row="{{ $i }}" data-role="distributor" value="{{ $range['distributor'] }}">
                            </td>
                            <td>
                                <input type="number" name="ranges[{{ $i }}][shop]" class="form-control margin-input" data-row="{{ $i }}" data-role="shop" value="{{ $range['shop'] }}">
                            </td>
                            <td id="admin_percent_{{ $i }}">0%</td>
                            <td id="company_value_{{ $i }}">₹0</td>
                            <td id="distributor_value_{{ $i }}">₹0</td>
                            <td id="shop_value_{{ $i }}">₹0</td>
                            <td id="admin_value_{{ $i }}">₹0</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="btn btn-primary mt-3">Save Margins</button>
        </form>
    @else
        <div class="alert alert-info">No margin data available for this product.</div>
    @endif
</div>

<script>
    function calculateAll() {
        const cost = parseFloat(document.getElementById('cost').value) || 0;
        const price = parseFloat(document.getElementById('price').value) || 0;
        const profit = price - cost;

        const rows = document.querySelectorAll('tbody tr');
        rows.forEach((row, index) => {
            const company = parseFloat(row.querySelector('[data-role="company"]').value) || 0;
            const distributor = parseFloat(row.querySelector('[data-role="distributor"]').value) || 0;
            const shop = parseFloat(row.querySelector('[data-role="shop"]').value) || 0;

            const admin = 100 - (company + distributor + shop);

            document.getElementById(`admin_percent_${index}`).innerText = admin.toFixed(2) + '%';
            document.getElementById(`company_value_${index}`).innerText = '₹' + ((profit * company) / 100).toFixed(2);
            document.getElementById(`distributor_value_${index}`).innerText = '₹' + ((profit * distributor) / 100).toFixed(2);
            document.getElementById(`shop_value_${index}`).innerText = '₹' + ((profit * shop) / 100).toFixed(2);
            document.getElementById(`admin_value_${index}`).innerText = '₹' + ((profit * admin) / 100).toFixed(2);
        });
    }

    document.querySelectorAll('.margin-input').forEach(input => {
        input.addEventListener('input', calculateAll);
    });

    window.addEventListener('load', calculateAll);
</script>

</body>
</html>