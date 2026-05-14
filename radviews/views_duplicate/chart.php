<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-tachometer" aria-hidden="true"></i> Dashboard
        <small>Control panel</small>
      </h1>
    </section>

<div class="box box-primary">


        <div class="col-md-6">
        <div class="box">

            <div class="box-header with-border">
                <h3 class="box-title">Data Chart</h3>


            </div>
            <div class="box-body">
                <div class="chart">
                    <canvas id="myChart1"></canvas>
                </div>
            </div>

            </div>

        </div>

        </div>
</div>




    <script type="text/javascript">

        var myChart1 = document.getElementById('myChart1').getContext('2d');

        // Global Options
        Chart.defaults.global.defaultFontFamily = 'Lato';
        Chart.defaults.global.defaultFontSize = 18;
        Chart.defaults.global.defaultFontColor = '#777';

        var massPopChart = new Chart(myChart1, {
        type:'bar', // bar, horizontalBar, pie, line, doughnut, radar, polarArea
        data:{
            labels:['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets:[{
            label:'Downloads',
            data:[
                617594,
                181045,
                153060,
                106519,
                105162,
                95072
            ],
            //backgroundColor:'green',
            backgroundColor:[
                'rgba(255, 99, 132, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(75, 192, 192, 0.6)',
                'rgba(153, 102, 255, 0.6)',
                'rgba(255, 159, 64, 0.6)',
                'rgba(255, 99, 132, 0.6)'
            ],
            borderWidth:1,
            borderColor:'#777',
            hoverBorderWidth:3,
            hoverBorderColor:'#000'
            }]
        },
        options:{
            title:{
            display:true,
            text:'Downloads/Uploads',
            fontSize:22,
            responsive: true
            },
            legend:{
            display:true,
            position:'right',
            labels:{
                fontColor:'#000'
            }
            },
            layout:{
            padding:{
                left:50,
                right:0,
                bottom:0,
                top:0
            }
            },
            tooltips:{
            enabled:true
            }



        }
        });

        

</script>