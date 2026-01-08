import Chart from "chart.js/auto";

const labels = ["Loket 1", "Loket 2", "Loket 3"];

const data = {
  labels: labels,
  datasets: [
    {
      label: "Jumlah Pelayanan",
      data: [200, 136, 65],
    },
  ],
};

const config = {
  type: "doughnut",
  data: data,
  options: {
    plugins: {
      legend: {
        position: "bottom",
      },
      title: {
        display: true,
        text: "Data Loket dengan Pelayanan Terbanyak",
        font: {
          family: "Inter",
          weight: 500,
        },
      },
    },
  },
};

new Chart(document.getElementById("data-loket"), config);
