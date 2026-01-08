const labels = [
  "Jan",
  "Feb",
  "Mar",
  "Apr",
  "Mei",
  "Jun",
  "Jul",
  "Agu",
  "Sep",
  "Okt",
  "Nov",
  "Des",
];

const data = {
  labels: labels,
  datasets: [
    {
      label: "Jumlah Kunjungan",
      backgroundColor: [
        "#FF2865",
        "#FF639F",
        "#FFB26B",
        "#FFF29C",
        "#FEFF89",
        "#FF9F68",
        "#F85959",
        "#7C203A",
        "#CD7856",
        "#E4663A",
        "#FFE35E",
        "#FBFF7C",
      ],
      data: [
        1500, 1250, 1450, 700, 1100, 600, 800, 1460, 1000, 660, 1346, 1440,
      ],
    },
  ],
};

const config = {
  type: "bar",
  data: data,
  options: {
    plugins: {
      legend: {
        display: false,
      },
      title: {
        display: true,
        text: "Data Kunjungan Terbanyak",
        font: {
          family: "Inter",
          weight: 500,
        },
      },
    },
  },
};

new Chart(document.getElementById("data-kunjungan"), config);
