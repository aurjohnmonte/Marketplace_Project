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
        <img src="../images/Logo.svg" alt="Timbershoppe Logo">
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
  min-height: 100vh;
  margin: 0;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  user-select: none;
  overflow: hidden;
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

.navbar .btn {
  padding: 0.75rem 1.5rem;
  font-size: 0.9rem;
  border-radius: 0.375rem;
  transition: all 0.2s ease;
  min-height: 44px; /* Better touch target */
  display: flex;
  align-items: center;
  justify-content: center;
}

/* for the content div (logo and form)) */
.content {
  display: flex;
  flex: 1 1;
  align-items: center;
  justify-content: space-between;
  padding: 0 2rem;
  overflow: auto;
  position: relative;
  z-index: 1;
  font-family: 'Roboto-Reg';
}

.content img {
    width: 25em;
}


/* Responsive Design */
@media (min-width: 901px) and (max-width: 1200px) {
  .content {
    gap: 15em;
  }
}

@media (max-width: 900px) {
  .content {
    flex-direction: column;
    justify-content: start;
    align-items: center;
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

@media (max-width: 768px)  and (min-width: 601px) {
  .navbar {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    padding: 1em;
  }
  .navbar h1 {
    font-size: 1.5rem;
    margin: 0;
    text-align: center;
  }
  .navbar ul {
    margin: 0 2em 0 0;
    padding: 0;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1em;
  }
  .navbar ul li {
    margin: 0;
  }
  .navbar .btn {
    font-size: 0.95rem;
    min-width: 120px;
  }
}

@media (min-width: 481px) and (max-width: 600px) {
  .container {
    min-width: 0;
    padding: 0;
  }
  .navbar {
    padding: 0.75rem;
    gap: 0.75rem;
  }
  .navbar h1 {
    font-size: 1.3rem;
  }
  .navbar ul {
    gap: 0.75rem;
    width: 100%;
    justify-content: space-between;
  }
  .navbar .btn {
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    min-width: 100px;
    flex: 1;
    max-width: 140px;
  }
  .content {
    flex-direction: column;
    gap: 0;
    padding: 1em;
    flex: 0;
  }
  .content img {
    max-width: 100vw;
  }
  .signup-container, .login-container {
    max-width: 100%;
  }
}

@media (max-width: 480px) {
  .navbar {
    padding: 0.5em 0 0 1em;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }
  .navbar h1 {
    font-size: 1rem;
    margin: 0;
    flex-shrink: 0;
  }
  .navbar ul {
    gap: 0;
    width: auto;
    flex-shrink: 0;
    justify-content: flex-end;
    margin: 0;
    padding: 0;
  }
  .navbar .btn {
    padding: 0;
    font-size: 0.7rem;
    width: auto;
    max-width: none;
    background-color: transparent;
    border: none;
    color: #9b6952;
    margin: 0;
  }
}
</style>
