<template>
    <div class="form-content">
        <form method="POST">
            <h1>LOGIN</h1>
            <div
                v-for="field in requiredInfo"
                :key="field.id"
                class="input-group"
            >
            <div class="input-box">
                <input
                :type="field.type"
                :name="field.id"
                :id="field.id"
                required
                />
                <label :for="field.id">{{ field.label }}</label>
            </div>
            </div>
            <button
                class="form-btn"
            >Login</button>
        </form>
    </div>
</template>

<script>
export default {
    data() {
        return {
            action: 'login',
            requiredInfo: [
                { id: 'username', label: 'Username', type: 'text' },
                { id: 'password', label: 'Password', type: 'password' }
            ],
            data: {
                username: '',
                password: ''
            }
        }
    }
}
</script>

<style>
/* Main container */
.login-container  {
  background-color:	#F5F5DC;
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 30%;
  margin-left: 8rem;
  border: 1px solid #000;
  border-radius: 10px;
}

.form-content{
  flex: 1;
  width: 100%;
  height: 100%;
  padding: 2rem 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.form-content form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
</style>
