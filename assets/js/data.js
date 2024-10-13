const ctx = document.getElementById("total");
Chart.register(ChartDataLabels);
new Chart(ctx, {
  type: "bar",
  data: {
    labels: [
      "Generasi 1",
      "Generasi 2",
      "Generasi 3",
      "Generasi 4",
      "Generasi 5",
      "Generasi 6",
      "Generasi 7",
      "Generasi 8",
      "Generasi 9",
      "Generasi 10",
      "Generasi 11",
      "Generasi 12",
      "JKT48V",
    ],
    datasets: [
      {
        label: "Total Member",
        data: [28, 31, 32, 12, 17, 14, 20, 18, 13, 11, 14, 17, 3],
      },
      {
        label: "Member Aktif",
        data: [0, 0, 2, 0, 0, 1, 6, 4, 3, 7, 12, 16, 3],
      },
    ],
  },
  options: {
    plugins: {
      legend: {
        position: "top",
        labels: {
          color: "white",
        },
      },
      datalabels: {
        anchor: "end", // Position of the labels (start, end, center, etc.)
        align: "end", // Alignment of the labels (start, end, center, etc.)
        color: "white", // Color of the labels
        font: {
          weight: "bold",
        },
        formatter: function (value, ctx) {
          return value; // Display the actual data value
        },
      },
    },
    indexAxis: "y",
    scales: {
      x: {
        ticks: {
          color: 'white',
        }
      },
      y: {
        ticks: {
          color: 'white',
        },
        beginAtZero: true,
      },
    },
    bar: {
      borderWidth: 4,
    },
    responsive: true,
  },
});

const ctx1 = document.getElementById("newera");
Chart.register(ChartDataLabels);
new Chart(ctx1, {
  type: "bar",
  data: {
    labels: [
        "Generasi 1",
        "Generasi 3",
        "Generasi 4",
        "Generasi 5",
        "Generasi 6",
        "Generasi 7",
        "Generasi 8",
        "Generasi 9"
      ],
      datasets: [
        {
          label: "Total Member",
          data: [1, 5, 3, 1, 2, 10, 7, 4],
        },
        {
          label: "Member Aktif",
          data: [0, 2, 0, 0, 1, 6, 4, 3],
        },
      ],
  },
  options: {
    indexAxis: "y",
    scales: {
      x: {
        ticks: {
          color: 'white',
        }
      },
      y: {
        ticks: {
          color: 'white',
        },
        beginAtZero: true,
      },
    },
    bar: {
      borderWidth: 4,
    },
    responsive: true,
    plugins: {
      datalabels: {
        anchor: "end", // Position of the labels (start, end, center, etc.)
        align: "end", // Alignment of the labels (start, end, center, etc.)
        color: "white", // Color of the labels
        font: {
          weight: "bold",
        },
        formatter: function (value, ctx1) {
          return value; // Display the actual data value
        },
      },
      legend: {
        position: "top",
        labels: {
          color: "white",
        },
      },
    },
  },
});
