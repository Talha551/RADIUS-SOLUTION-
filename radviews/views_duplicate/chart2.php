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
                        <canvas id="bar-chart-grouped" width="800" height="400"></canvas>
                    </div>
                </div>

            </div>

        </div>

        <div class="col-md-6">
            <div class="box">

                <div class="box-header with-border">
                    <h3 class="box-title">Data Chart</h3>
                </div>
                <div class="box-body">
                    <div class="chart">
                        <canvas id="line-chart" width="800" height="400"></canvas>
                    </div>
                </div>

            </div>

        </div>

    </div>
        

</div>

<script type="text/javascript">

    new Chart(document.getElementById("bar-chart-grouped"), {
        type: 'bar',
        data: {
        labels: ["1900", "1950", "1999", "2050"],
        datasets: [
            {
            label: "Africa",
            backgroundColor: "#3e95cd",
            data: [133,221,783,2478]
            }, {
            label: "Europe",
            backgroundColor: "#8e5ea2",
            data: [408,547,675,734]
            }
        ]
        },
        options: {
        title: {
            display: true,
            text: 'Population growth (millions)'
        }
        }
    });

    new Chart(document.getElementById("line-chart"), {
    type: 'line',
    data: {
        labels: [1500,1600,1700,1750,1800,1850,1900,1950,1999,2050],
        datasets: [{ 
            data: [86,114,106,106,107,111,133,221,783,2478],
            label: "Africa",
            borderColor: "#3e95cd",
            fill: false
        }, { 
            data: [282,350,411,502,635,809,947,1402,3700,5267],
            label: "Asia",
            borderColor: "#8e5ea2",
            fill: false
        }, { 
            data: [168,170,178,190,203,276,408,547,675,734],
            label: "Europe",
            borderColor: "#3cba9f",
            fill: false
        }, { 
            data: [40,20,10,16,24,38,74,167,508,784],
            label: "Latin America",
            borderColor: "#e8c3b9",
            fill: false
        }, { 
            data: [6,3,2,2,7,26,82,172,312,433],
            label: "North America",
            borderColor: "#c45850",
            fill: false
        }
        ]
    },
    options: {
        title: {
        display: true,
        text: 'World population per region (in millions)'
        }
    }
    });



</script>