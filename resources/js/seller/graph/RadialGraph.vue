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
