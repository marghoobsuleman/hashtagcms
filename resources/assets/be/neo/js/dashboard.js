window.Dashboard = {
    data:[],
    labelsCategories:[],
    datasCategories:[],
    labelsContent:[],
    datasContent:[],
    bgColors:[
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
    ],
    borderColors: [
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
    ],
    createChart: function (id, labels, datas, bgColors, boderColors) {
        //console.log(id, labels, datas);
        let ctx = document.getElementById(id);
        //console.log(ctx);
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
    },
    makeData: function () {
        for(let i=0,len=this.data.categories.length;i<len;i++) {
            this.labelsCategories.push(this.data.categories[i].link_rewrite);
            this.datasCategories.push(this.data.categories[i].read_count);
        }
    },
    init: function (data) {
        this.data = data;
        this.makeData();
        this.createChart('topCatgories',this.labelsCategories, this.datasCategories, this.bgColors, this.borderColors);
        this.createChart('topContents',this.labelsContent, this.datasContent, [...this.bgColors].reverse(), [...this.borderColors].reverse());
    }
}
