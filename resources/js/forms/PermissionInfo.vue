<template>
  <div class="permission-dialog__container">
    <div class="header-soy">
        <img src="../../images/cancel.png" style="height: 15px; width: 15px;" @click="goCancel()">
    </div>
    <div class="permission-dialog__content">
      <h2 class="permission-dialog__title">Location Access Required</h2>
      <p class="permission-dialog__text">
        To ensure the most accurate and efficient service, we require access to your current location. This allows us to tailor your experience based on where you are. Specifically:
      </p>

    <ol class="permission-dialog__list">
        <li>
            <strong>A.</strong> <span class="permission-dialog__point-title">Personalized Proximity-Based Service</span><br />
            By accessing your current location, we are able to dynamically identify the shop that is geographically closest to you. This ensures that you are not only shown the most relevant options but also experience faster service, reduced travel time, and more localized support. Without this information, we can only provide generic suggestions, which may not reflect actual availability or proximity.
        </li>

        <li>
            <strong>B.</strong> <span class="permission-dialog__point-title">Precise Shop Location Mapping</span><br />
            Your location allows us to pinpoint both your position and that of nearby shops with high accuracy. This eliminates confusion caused by vague map markers or outdated manual inputs. It ensures that directions, walking or driving estimates, and route suggestions are all calculated in real time, using live geospatial data — reducing navigation errors and improving overall reliability.
        </li>

        <li>
            <strong>C.</strong> <span class="permission-dialog__point-title">Seamless Navigation and Future Enhancements</span><br />
            With your permission, we can integrate directly with map applications and offer turn-by-turn directions, saving you time and effort. Additionally, having access to real-time location data enables us to improve future features such as estimated arrival times, store traffic monitoring, and geo-fenced promotions — all designed to elevate your in-app experience without compromising privacy.
        </li>
    </ol>


      <p class="permission-dialog__text">
        Location data is only used temporarily during your session and is never stored or shared without your explicit consent. <br /><br />
        By continuing, you acknowledge that you agree to our
        <strong>Terms</strong> and <strong>Conditions</strong>.
      </p>

      <div class="permission-dialog__actions">
        <button class="permission-dialog__btn permission-dialog__btn--allow" @click="goAllow()">Allow Access</button>
        <button class="permission-dialog__btn permission-dialog__btn--cancel" @click="goCancel()">Cancel</button>
      </div>
    </div>
  </div>
</template>

<script>
export default{
    data(){
        return{

        }
    },
    methods: {
      goAllow(){
        this.$emit('goAllow');
      },
        goCancel(){
            this.$emit('goCancel');
        }
    }
}
</script>

<style scoped>
.permission-dialog__container {
  position: absolute;
  top: 10%;
  left: 10%;
  width: 500px;
  background-color: #ffffff;
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
  border-radius: 8px;
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  border: 1px solid #ddd;
}

.permission-dialog__content {
  font-family: 'Segoe UI', Roboto, sans-serif;
  color: #333;
  position: relative;
  height: auto;
  overflow-y: scroll;
  height: 500px;
}

.permission-dialog__title {
  font-size: 1.6rem;
  text-align: center;
  font-weight: 600;
  margin-top: 50px;
}

.permission-dialog__text {
  font-size: 1rem;
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.permission-dialog__list {
  list-style: none;
  padding-left: 0;
  margin-bottom: 1.5rem;
}

.permission-dialog__list li {
  margin-bottom: 1rem;
  font-size: 0.97rem;
  line-height: 1.5;
}

.permission-dialog__point-title {
  display: inline-block;
  font-weight: 600;
  margin-top: 0.3rem;
}

strong {
  font-weight: 600;
}

.permission-dialog__actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.permission-dialog__btn {
  padding: 0.6rem 1.2rem;
  font-size: 0.95rem;
  border: 1px solid transparent;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.2s ease, border 0.2s ease;
}

.permission-dialog__btn--allow {
  background-color: #0069d9;
  color: #fff;
  border-color: #0062cc;
}

.permission-dialog__btn--allow:hover {
  background-color: #0056b3;
}

.permission-dialog__btn--cancel {
  background-color: #f8f9fa;
  color: #333;
  border-color: #ccc;
}

.permission-dialog__btn--cancel:hover {
  background-color: #e2e6ea;
}
.header-soy{
    top: 2%;
    left: 90%;
    position: absolute;
    cursor: pointer;
}
</style>