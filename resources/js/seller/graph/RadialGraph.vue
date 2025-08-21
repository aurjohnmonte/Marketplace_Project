<template>
  <div>
    <PolarAreaChart :data="chartData" :options="chartOptions" />
    <div style="display: grid; grid-template-columns: 200px 50px 10px; margin-top: 10px;" v-for="i in products_data_radial.labels.length" :key="i">
      <label>{{ products_data_radial.labels[i-1] }}</label><label>-</label><label>{{ products_data_radial.data[products_data_radial.labels[i-1]] }}</label>
    </div>
  </div>
</template>

<script>
import { PolarArea } from 'vue-chartjs'
import { useDataStore } from '../../stores/dataStore'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  RadialLinearScale,
  ArcElement
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, RadialLinearScale, ArcElement)

export default {
  props: ['products_data_radial'],
  components: { PolarAreaChart: PolarArea },
  data() {
    return {
      dataset: [],
      colors: [
                "#FF5733", // red-orange
                "#33FF57", // green
                "#3357FF", // blue
                "#F39C12", // orange
                "#8E44AD", // purple
                "#1ABC9C", // teal
                "#E74C3C", // red
                "#2ECC71", // light green
                "#3498DB", // light blue
                "#9B59B6", // violet
                "#34495E", // dark gray-blue
                "#16A085", // dark teal
                "#27AE60", // emerald
                "#2980B9", // royal blue
                "#D35400", // dark orange
                "#C0392B", // dark red
                "#7F8C8D", // gray
                "#BDC3C7", // silver
                "#E67E22", // carrot orange
                "#95A5A6"  // light gray
              ],
      chartData: {
        labels: ['Red', 'Blue', 'Yellow', 'Green'],
        datasets: [
          {
            label: 'Values',
            data: [11, 16, 7, 3],
            backgroundColor: this.colors
          }
        ]
      },
      chartOptions: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    }
  },
  methods: {
    initializeGraph(){

      this.chartData = {
        labels: this.products_data_radial.labels,
        datasets: [
          {
            label: 'Reviews',
            data: this.dataset,
            backgroundColor: this.colors
          }
        ]
      }
    }
  },
  mounted(){
    this.initializeGraph();
    console.log('naa diri: ', this.products_data_radial);
    for(let i in this.products_data_radial.data){
      this.dataset.push(this.products_data_radial.data[i]);
    }


    console.log(this.products_data_radial);
  }
}
</script>

<style scoped>
.radial-graph-container {
  width: 100%;
  height: 100%;
  position: relative;
}

.radial-graph-container canvas {
  max-width: 100%;
  height: auto;
}

.graph-legend {
  display: grid;
  grid-template-columns: 200px 50px 10px;
  margin-top: 10px;
  gap: 0.5rem;
}

.graph-legend label {
  font-size: 0.9rem;
  color: #333;
  font-weight: 500;
}

/* ===== RESPONSIVE DESIGN ===== */

/* Small mobile devices */
@media (max-width: 480px) {
  .radial-graph-container {
    min-height: 200px;
  }

  .radial-graph-container canvas {
    max-height: 150px;
  }

  .graph-legend {
    grid-template-columns: 120px 30px 5px;
    margin-top: 8px;
    gap: 0.3rem;
  }

  .graph-legend label {
    font-size: 0.7rem;
  }
}

/* Mobile - Medium devices */
@media (max-width: 768px) {
  .radial-graph-container {
    min-height: 250px;
  }

  .radial-graph-container canvas {
    max-height: 200px;
  }

  .graph-legend {
    grid-template-columns: 150px 40px 8px;
    margin-top: 10px;
    gap: 0.4rem;
  }

  .graph-legend label {
    font-size: 0.8rem;
  }
}

/* Tablet devices */
@media (min-width: 769px) and (max-width: 1024px) {
  .radial-graph-container {
    min-height: 300px;
  }

  .radial-graph-container canvas {
    max-height: 250px;
  }

  .graph-legend {
    grid-template-columns: 180px 45px 10px;
    margin-top: 12px;
    gap: 0.5rem;
  }

  .graph-legend label {
    font-size: 0.85rem;
  }
}

/* Desktop devices */
@media (min-width: 1025px) {
  .radial-graph-container {
    min-height: 350px;
  }

  .radial-graph-container canvas {
    max-height: 300px;
  }

  .graph-legend {
    grid-template-columns: 200px 50px 10px;
    margin-top: 15px;
    gap: 0.6rem;
  }

  .graph-legend label {
    font-size: 0.9rem;
  }
}

/* Large desktop devices */
@media (min-width: 1440px) {
  .radial-graph-container {
    min-height: 400px;
  }

  .radial-graph-container canvas {
    max-height: 350px;
  }

  .graph-legend {
    grid-template-columns: 220px 55px 12px;
    margin-top: 18px;
    gap: 0.7rem;
  }

  .graph-legend label {
    font-size: 1rem;
  }
}

/* Landscape orientation for mobile */
@media (max-width: 768px) and (orientation: landscape) {
  .radial-graph-container {
    min-height: 180px;
  }

  .radial-graph-container canvas {
    max-height: 130px;
  }

  .graph-legend {
    grid-template-columns: 120px 30px 5px;
    margin-top: 6px;
  }
}

/* High DPI displays */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
  .radial-graph-container canvas {
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
  }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
  .radial-graph-container {
    min-height: 250px;
  }
}

/* Print styles */
@media print {
  .radial-graph-container {
    min-height: 250px;
    break-inside: avoid;
  }

  .graph-legend {
    grid-template-columns: 150px 40px 8px;
  }
}
</style>
