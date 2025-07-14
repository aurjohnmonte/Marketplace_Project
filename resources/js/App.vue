<template>

<div class="container">
    <nav class="navbar navbar-light bg-light">
        <h1>Timbershoppe</h1>
        <ul>
            <li><button class="btn style" @click="action = 'signup'">Sign Up</button></li>
            <li><button class="btn style" @click="action = 'login'">Login</button></li>
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

<style>
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
  justify-content: center;
  padding: 0 2rem;
  overflow: auto;
  position: relative;
  z-index: 1;
  font-family: 'Roboto-Reg';
  gap: 10em;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .content {
    gap: 5em;
  }
}

@media (max-width: 900px) {
  .content {
    flex-direction: column;
    align-items: center;
    gap: 2em;
    padding: 1rem;
  }
  .content img {
    width: 160px;
    margin-bottom: 1.5rem;
    max-width: 60vw;
    height: auto;
  }
  .signup-container, .login-container {
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
  }
}

@media (max-width: 600px) {
  .container {
    min-width: 0;
    padding: 0;
  }
  .navbar {
    flex-direction: column;
    align-items: flex-start;
    padding: 1rem 1rem 0;
  }
  .navbar h1 {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
    word-break: break-word;
  }
  .navbar ul {
    margin: 0 0 0.5rem 0;
    padding: 0;
  }
  .navbar ul li {
    margin-right: 1rem;
  }
  .content {
    flex-direction: column;
    gap: 1em;
    padding: 0.5rem;
    align-items: stretch;
  }
  .content img {
    width: 90px;
    margin-bottom: 1rem;
    max-width: 80vw;
    height: auto;
  }
  .signup-container, .login-container {
    max-width: 100%;
    padding: 0 0.5rem;
  }
  .navbar ul li {
    display: block;
    margin: 0 0 0.5rem 0;
  }
}
</style>
