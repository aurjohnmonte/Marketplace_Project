<template>

<div class="container">
    <nav class="navbar navbar-light bg-light">
        <h1>Timbershoppe</h1>
        <ul>
            <li><button class="btn" @click="action = 'signup'">Sign Up</button></li>
            <li><button class="btn" @click="action = 'login'">Login</button></li>
        </ul>
    </nav>

    <!--  Body of the Page  -->
    <div class="content">
        <img src="../images/Logo.svg" alt="">
        <div class="signup-container"
            v-if="action === 'signup'"
        >
            <Signup/>
        </div>
        <div class="login-container" v-else>
            <Login/>
        </div>
    </div>
</div>
</template>

<script>
// Importing the Components
import Login from './forms/Login.vue';
import Signup from './forms/Signup.vue';
import '../css/app.css';

export default {
  components: {
    Login,
    Signup
  },
  data(){
    return {
        action: null
    }
  }
}
</script>

<style scoped>
.container {
  width: 100%;
  min-width: 320px;
  height: 100vh;
  margin: 0;
  display: flex;
  flex-direction: column;
}

.navbar {
  display: flex;
  font-family: Decoy-Med;
  text-transform: uppercase;
  width: 100%;
  align-items: center;
  padding: 2rem 2rem 0;
  flex-shrink: 0;
}

.navbar ul {
  margin-left: auto;
  margin-right: 2rem;
}
.navbar ul li{
  list-style: none;
  display: inline-block;
  margin-right: 2rem;
}

/* for the content div (logo and form)) */
.content {
  display: flex;
  flex: 1;
  align-items: center;
  gap: 15em;
  padding: 0 2rem;
  overflow: auto;
  position: relative;
  z-index: 1;
  font-family: 'Roboto-Reg';
}
</style>
