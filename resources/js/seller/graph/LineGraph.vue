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
