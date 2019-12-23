var drawChart;

drawChart = function() {
  var chart, chartData, chartObj, charts, colors, envParams, friendOptions, name, options, results;
  colors = {
    gray: '#626262',
    red: '#e83217',
    green: '#3cb145',
    blue: '#0071be',
    yellow: '#f8b308',
    purple: '#552a8d',
    pink: '#ff5da7',
    facebook: '#4e69a2',
    twitter: '#55acee'
  };
  options = {
    chartArea: {
      top: 10,
      width: '80%',
      height: '80%'
    }
  };
  friendOptions = {
    chartArea: {
      height: '85%'
    },
    legend: 'none',
    vAxis: {
      format: '#人'
    },
    series: [
      {
        color: colors.twitter
      }
    ]
  };
  envParams = {
    type: 'PieChart',
    options: {
      pieSliceText: 'value'
    }
  };
  charts = {
    gender: {
      type: 'PieChart',
      options: {
        pieSliceText: 'value',
        legend: {
          position: 'bottom',
          alignment: 'center'
        },
        slices: [
          {
            color: colors.blue
          }, {
            color: colors.red
          }, {
            color: colors.gray
          }
        ]
      }
    },
    age: {
      type: 'BarChart',
      options: {
        chartArea: {
          top: 0,
          height: '90%'
        },
        hAxis: {
          format: '#人'
        },
        series: [
          {
            color: colors.blue
          }, {
            color: colors.red
          }
        ]
      }
    },
    time: {
      type: 'ColumnChart',
      options: {
        chartArea: {
          width: '90%',
          height: '85%'
        },
        legend: 'none',
        vAxis: {
          format: '#人'
        },
        series: [
          {
            color: colors.yellow
          }
        ]
      }
    },
    'facebook-friend': {
      type: 'ColumnChart',
      options: $.extend({}, friendOptions, {
        series: [
          {
            color: colors.facebook
          }
        ]
      })
    },
    'twitter-followers': {
      type: 'ColumnChart',
      options: $.extend({}, friendOptions, {
        series: [
          {
            color: colors.twitter
          }
        ]
      })
    },
    env: {
      type: 'PieChart',
      options: {
        pieSliceText: 'value',
        legend: {
          position: 'bottom',
          alignment: 'center'
        }
      }
    },
    'env-desktop-os': envParams,
    'env-desktop-browser': envParams,
    'env-mobile-os': envParams,
    'env-mobile-browser': envParams
  };
  results = [];
  for (name in charts) {
    chart = charts[name];
    chartData = new google.visualization.DataTable(window.chartDatas[name]);
    chartObj = new google.visualization[chart.type](document.getElementById('chart-' + name));
    results.push(chartObj.draw(chartData, $.extend(true, {}, options, chart.options)));
  }
  return results;
};

google.load('visualization', '1', {
  packages: ['corechart']
});

google.setOnLoadCallback(drawChart);
