<template>

<div class="container">
    <nav class="navbar navbar-light bg-light">
        <h1>Craftify</h1>
        <ul>
            <li><button class="btn style" @click="action = 'signup'">Sign Up</button></li>
            <li><button class="btn style" @click="action = 'login'">Login</button></li>
        </ul>
    </nav>

    <!--  Body of the Page  -->
    <div class="content">
        <img src="../images/Logo-3.png" alt="Craftify Logo">
        <div class="signup-container" v-if="action === 'signup'">
            <!-- Signup warnings displayed at top of signup container -->
            <div v-if="signupErrors.length" class="form-warnings signup-warning">
                <svg data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"></path>
                </svg>
                <ul>
                <li v-for="(msg, i) in signupErrors" :key="i">{{ msg }}</li>
                </ul>
            </div>
            <Signup @update-signup-errors="updateSignupErrors"/>
        </div>
        <div class="login-container" v-if="action === 'login'">
            <!-- This is for the warning messages -->
            <div v-if="loginErrors.length" class="form-warnings login-warning">
                <svg data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"></path>
                </svg>
                <ul>
                    <li v-for="(msg, i) in loginErrors" :key="i">{{ msg }}</li>
                </ul>
            </div>
            <Login @update-errors="updateLoginErrors"/>
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
      action: 'login',
      loginErrors: [],
      signupErrors: []
    }
  }
    ,
    methods: {
      updateLoginErrors(errors) {
        this.loginErrors = errors;
      }
      ,
      updateSignupErrors(errors) {
        this.signupErrors = errors;
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
  user-select: none;
  overflow: hidden;
}

.navbar {
  display: flex;
  font-family: 'Merriweather', serif;
  font-weight: 800;
  font-size: 1.2em;
  color: #2b2b2b;
  text-align: center;
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
    width: 35em;
}

/* Main container */
.login-container {
  background-color:	#F5F5DC;
  display: flex;
  flex-direction: column;
  height: 20em;
  width: 30%;
  min-width: 320px;
  max-width: 600px;
  margin: 0 5em 0 0;
  padding: 1.5em;
  border: 1px solid #000;
  border-radius: 10px;
  border-radius: 10px;
  box-sizing: border-box;
  align-items: center;
}

/* make signup container a positioning context so absolute warning positions relative to it */
.signup-container {
  position: relative;
}


/* error message design */
.error-message {
    color: red;
    font-size: 11px;
    margin-top: 4px;
    line-height: 1.3;
    word-wrap: break-word;
    margin-left: .2em;
}


.form-warnings {
  width: 27%;
  margin: .5em 0 0;
  padding: 0.8em;
  background: #ffe6e6;
  border: 1px solid #ff4d4d;
  border-radius: .8em;
  color: #cc0000;
  font-size: 0.8rem;
  display: flex;
  align-items: center;
  gap: 1em;
}

/* variant used when displaying on top of login card */
.form-warnings.login-warning {
  position: absolute;
  top: 8%;
  right: 7rem;
  z-index: 5;
}

.form-warnings.signup-warning {
  /* Default placement for large screens: positioned near the signup form */
  position: absolute;
  width: 35%;
  top: 6%;
  right: 34%;
  z-index: 5;
}

/* Wider desktop: nudge a bit to maintain spacing when content is wide */
@media (min-width: 1400px) {
  .form-warnings.signup-warning {
    width: 96%;
    top: -25%;
    right: 0%;
  }
}

/* Laptop screens: show the warning above the signup card and center it relative to the card */
@media (min-width: 901px) and (max-width: 1399px) {
  .form-warnings.signup-warning {
    position: absolute;
    width: 96%;
    top: -5.8em;
    left: 50%;
    transform: translateX(-50%);
  }
}

/* Tablet / small laptop: keep the warning above the card and nearly full width of the card */
@media (min-width: 601px) and (max-width: 900px) {
  .form-warnings.signup-warning {
    position: absolute;
    width: 92%;
    top: -10.25em;
    left: 4%;
    right: 4%;
    transform: none;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
  }
}

.form-warnings svg {
    width: 3em;
    margin-left: 1em;
}

.form-warnings ul {
  padding-left: 1.2em;
  margin: 0;
  list-style: none;
}

.form-warnings ul li a {
  margin-bottom: 0.3em;
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
  }
  /* make warnings flow normally on small screens */
  .form-warnings.login-warning,
  .form-warnings.signup-warning {
    width: auto;
    top: 10em;
    left: auto;
    right: auto;
    max-width: 100%;
  }
}

/* iPad specific adjustments (covers both portrait and landscape roughly 768px - 1024px) */
@media (min-width: 768px) and (max-width: 1024px) {
  .content {
    flex-direction: row;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    padding: 1rem 2rem;
  }

  .content img {
    width: 28rem;
    max-width: 45%;
    height: auto;
  }

  .signup-container, .login-container {
    width: 42%;
    min-width: 360px;
    max-width: 520px;
    margin: 0;
  }

  .form-warnings.signup-warning {
    position: absolute;
    width: 88%;
    top: -5.5em;
    left: 50%;
    transform: translateX(-50%);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
  }

  .form-warnings.login-warning {
    position: absolute;
    top: 6%;
    right: 3rem;
    width: 34%;
    max-width: 360px;
  }

  .form-warnings svg { width: 2.4em; }
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

@media (max-width: 780px) {
    .login-container{
        padding: 1em;
        margin: 0;
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
