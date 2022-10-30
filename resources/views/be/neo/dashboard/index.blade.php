@extends(htcms_admin_config('theme').'.index')

@section('content')

    <div class="row border-bottom">
        <div class="col-md-6">
            <h3>

                {!! htcms_get_module_name(request()->module_info) !!}

            </h3>
        </div>
        <div class="pull-right back-link margin-bottom-05">

            <cms-module-dropdown
                    data-modules="{{json_encode(request()->module_info)}}"
            ></cms-module-dropdown>

        </div>
    </div>
    <div class="container">
        <div class="row" style="margin-top: 10px">
            @foreach($data as $row)
                <div class="col-auto">
                    <info-box data-title="{{$row['label']}}"
                              data-sub-title="{{$row['total']}}"
                              data-icon-css="{{$row['icon']}}"
                              data-link="{{htcms_admin_path($row['link'])}}"
                    ></info-box>
                </div>
            @endforeach
        </div>
        <div class="row" style="padding: 10px;">
            <div class="col-lg-5">
                <h2>Top Categories</h2>
                <canvas id="topCatgories" width="100%" height="100"></canvas>
            </div>
            <div class="col-lg-5">
                <h2>Top Contents</h2>
                <canvas id="topContents" width="100%" height="100"></canvas>
            </div>
        </div>

    </div>
@endsection
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js" integrity="sha512-Wt1bJGtlnMtGP0dqNFH1xlkLBNpEodaiQ8ZN5JLA5wpc1sUlk/O5uuOMNgvzddzkpvZ9GLyYNa8w2s7rqiTk5Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>

        (function () {
            let data = <?php echo $graphData; ?>;
            let bgColors =  [
                'rgba(93, 128, 96, 0.7)',
                'rgba(135, 188, 118, 0.7)',
                'rgba(146, 222, 184, 0.7)',
                'rgba(135, 131, 222, 0.7)',
                'rgba(128, 57, 5, 0.7)',
                'rgba(231, 191, 200, 0.7)',
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)',
                'rgba(255, 159, 64, 0.7)'
            ];
            let borderColors = [
                'rgba(82, 117, 85, 1)',
                'rgba(115, 172, 96, 1)',
                'rgba(113, 204, 158, 1)',
                'rgba(103, 99, 206, 1)',
                'rgba(112, 51, 7, 1)',
                'rgba(213, 158, 170, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ];

            function createChart(id, labels, datas, bgColors, boderColors) {
                let ctx = document.getElementById(id);
                let myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: '# of Reads',
                            data: datas,
                            backgroundColor:bgColors,
                            borderColor: boderColors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            let labelsCategories = [];
            let datasCategories = [];
            for(let i=0,len=data.categories.length;i<len;i++) {
                labelsCategories.push(data.categories[i].link_rewrite);
                datasCategories.push(data.categories[i].read_count);
            }
            createChart('topCatgories',labelsCategories, datasCategories, bgColors, borderColors);

            let labelsContent = [];
            let datasContent = [];
            for(let i=0,len=data.pages.length;i<len;i++) {
                labelsContent.push(data.pages[i].link_rewrite);
                datasContent.push(data.pages[i].read_count);
            }
            createChart('topContents',labelsContent, datasContent, [...bgColors].reverse(), [...borderColors].reverse());
        })();
    </script>
@endpush
