
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Voucher Printer</title>
    <style type="text/css">

        body, html {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        #controls {
            margin: 10px;
        }

        #voucherContainer {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* Default to 4 columns */
            gap: 10px;
            padding: 20px;
        }

        .voucher {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            page-break-inside: avoid;
            border: 2px solid #ccc;
            padding: 0px;
        }

        .voucher-number, .qr-code {
            flex: 1;
            padding: 10px;
            text-align: left;
        }

        .qr-code {
            margin-left: 1px;
            margin-right: 1px;
            padding: 1px;
        }

        @media print {
            #controls {
                display: none;
            }

            body, html {
                width: 210mm;
                height: 297mm;
                margin: 0 auto;
            }

            
        }
        


    </style>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>

</head>
<body>
    <div id="controls">
        <button onclick="addColumn()">Add Column</button>
        <button onclick="removeColumn()">Remove Column</button>
        <button onclick="window.print()">Print Vouchers</button>

    </div>
    <div id="voucherContainer">
                <?php
                    if(!empty($cardsData))
                    {
                        $row_count = 1;
                        foreach($cardsData as $record)
                        {
                ?>
                        
                        <div class="voucher"><div class="info"><span><small>Batch <?php echo ($record->timebaseexp == 2) ? $record->series."<br>Duration: ".$record->expiretime.' days' : $record->series."<br>Duration: ".$record->expiretime.' month'; ?></small></span><br><span class="voucher-number"><b><?php echo "Code: "; echo $record->cardnum.""; ?></b></span></div><div class="qr-code"></div></div>
                <?php
                    $row_count++;
                    //echo $record->username."  -   ".$record->verified."     ";
                    //exit;
                    }
                        } 
                ?>

        
    </div>
    
    <script src="script.js"></script>
    <script>
        
        function addColumn() {
            const container = document.getElementById('voucherContainer');
            const currentColumns = getComputedStyle(container).gridTemplateColumns.split(' ').length;
            if (currentColumns < 12) { // Limit to a reasonable maximum
                container.style.gridTemplateColumns = `repeat(${currentColumns + 1}, 1fr)`;
            }
        }

        function removeColumn() {
            const container = document.getElementById('voucherContainer');
            const currentColumns = getComputedStyle(container).gridTemplateColumns.split(' ').length;
            if (currentColumns > 1) { // Ensure at least 1 column remains
                container.style.gridTemplateColumns = `repeat(${currentColumns - 1}, 1fr)`;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            var vouchers = document.querySelectorAll('.voucher');
            vouchers.forEach(function(voucher) {
                var number = voucher.querySelector('.voucher-number').textContent;
                var qrCodeContainer = voucher.querySelector('.qr-code');
                new QRCode(qrCodeContainer, {
                    text: number,
                    width: 64,
                    height: 64,
                    colorDark : "#000000",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.H
                });
            });
        });

    </script>
</body>
</html>
