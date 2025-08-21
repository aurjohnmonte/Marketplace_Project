<template>
  <div style="width: 100%; height: 400px; object-fit: contain; " v-if="chartData && chartOptions">
    <LineChart :data="chartData" :options="chartOptions" style="position: relative;"/>
  </div>
</template>

<script>
import { useDataStore } from '../../stores/dataStore'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement)

export default {
  components: { LineChart: Line },
  data() {
    return {
      chartData: null,
      chartOptions: null,
      store: useDataStore(),
      products_arr_date: [],
      shopreviews_arr_date: [],
      review_30days: {
        total_reviews: 0,

      },
    }
  },
  methods: {

    return30Days() {
        const arr_date = [];
        const today = new Date();

        for (let i = 0; i < 30; i++) {
            const d = new Date();
            d.setDate(today.getDate() - i);

            const formatted = d.toISOString().split("T")[0];
            arr_date.push(formatted);
            this.products_arr_date.push(0);
            this.shopreviews_arr_date.push(0);
        }
        console.log(arr_date);

        this.processProductsReviews(arr_date);
        this.processShopsReviews(arr_date);

        this.chartData = {
            labels: arr_date,
            datasets: [
                {
                    label: 'Shop reviews',
                    data: this.shopreviews_arr_date.reverse(),
                    borderColor: '#42b983',
                    backgroundColor: 'white',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'All products reviews',
                    data: this.products_arr_date.reverse(),
                    borderColor: '#2591DB',
                    backgroundColor: 'white',
                    tension: 0.4,
                    fill: true
                }
            ]
        };

        this.chartOptions = {
            responsive: true,
            plugins: {
                legend: {
                position: 'top'
                },
                title: {
                display: true,
                text: '30 days Reviews',
                color: '#fff'
                }
            },
            layout: {
                padding: 10
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.2)'
                    },
                    ticks: {
                        color: '#CACACA'
                    }
                    },
                y: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.2)'
                    },
                    ticks: {
                        color: '#fff'
                    }
                }
            },
            borderColor: '#ffffff',
            borderWidth: 2,
            borderDash: [],
        };


        return arr_date.reverse();
    },
    processShopsReviews(date){

        const reviews = this.store.selected_shop.reviews.filter(e => e.review_type === "shop");
        console.log('reviews: ', reviews);

        const today = new Date();
        let result;
        for(let review of reviews){

            let date = new Date(review.created_at);

            console.log(today.getDate() - date.getDate() + " : " + date.toLocaleDateString());

            result = today.getDate() - date.getDate();
            if(result <= 30){

                this.shopreviews_arr_date[result] = this.shopreviews_arr_date[result]+1;
            }
        }

        console.log('ito soy: ', this.shopreviews_arr_date);
    },
    processProductsReviews(date){

        const products = this.store.selected_shop.products;
        const arr_reviews = [];
        for(let product of products){
            console.log(product);
            for(let review of product.reviews){
                arr_reviews.push(review);
            }
        }

        console.log('arr_reviews', arr_reviews);

        const today = new Date();
        let result;
        for(let review of arr_reviews){

            let date = new Date(review.created_at);

            console.log(today.getDate() - date.getDate() + " : " + date.toLocaleDateString());

            result = today.getDate() - date.getDate();
            if(result <= 30){

                this.products_arr_date[result] = this.products_arr_date[result]+1;
            }
        }

        console.log('ito soy: ', this.products_arr_date);
    }

  },
  mounted(){

    this.return30Days();
  }
}
</script>

<style scoped>
.line-graph-container {
  width: 100%;
  height: 100%;
  position: relative;
}

/* ===== RESPONSIVE DESIGN ===== */

/* Small mobile devices */
@media (max-width: 480px) {
  .line-graph-container {
    min-height: 250px;
  }

  .line-graph-container canvas {
    max-height: 200px;
  }
}

/* Mobile - Medium devices */
@media (max-width: 768px) {
  .line-graph-container {
    min-height: 300px;
  }

  .line-graph-container canvas {
    max-height: 250px;
  }
}

/* Tablet devices */
@media (min-width: 769px) and (max-width: 1024px) {
  .line-graph-container {
    min-height: 350px;
  }

  .line-graph-container canvas {
    max-height: 300px;
  }
}

/* Desktop devices */
@media (min-width: 1025px) {
  .line-graph-container {
    min-height: 400px;
  }

  .line-graph-container canvas {
    max-height: 350px;
  }
}

/* Large desktop devices */
@media (min-width: 1440px) {
  .line-graph-container {
    min-height: 450px;
  }

  .line-graph-container canvas {
    max-height: 400px;
  }
}

/* Landscape orientation for mobile */
@media (max-width: 768px) and (orientation: landscape) {
  .line-graph-container {
    min-height: 200px;
  }

  .line-graph-container canvas {
    max-height: 150px;
  }
}

/* High DPI displays */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
  .line-graph-container canvas {
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
  }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
  .line-graph-container {
    min-height: 300px;
  }
}

/* Print styles */
@media print {
  .line-graph-container {
    min-height: 300px;
    break-inside: avoid;
  }
}
</style>
