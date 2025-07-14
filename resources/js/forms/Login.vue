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
                class="form-btn style"
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

/* for each row */
.input-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  margin-bottom: 1rem;
}

/* for the input */
.input-box {
  display: flex;
  flex-direction: column;
  position: relative;
}

.input-box input,
.input-box select {
  width: 100%;
  padding:  10px;
  font-size: 12px;
  border: 1px solid #9e363a;
  border-radius: 8px;
  background-color: #ffffff;
  color: #000000;
  box-sizing: border-box;
}
/* for the label */
.input-box label {
  position: absolute;
  top: 12px;
  padding-left: 12px;
  font-size: 13px;
  color: #575454;
  background-color: #ffffff;
  width: 70%;
  margin: 0 10px;
  pointer-events: none;
  transition: 0.2s ease all;
}

/* input and label animation */
.input-box label.floated {
  top: -8px;
  left: 10px;
  width: fit-content;
  font-size: 10px;
  padding: 0 10px;
  background-color: #091f36;
  border: #9e363a 1px solid;
  border-radius: 10px;
  color: #ffffff;
}

.input-box input:focus + label,
.input-box input.has-content + label {
  top: -8px;
  left: 10px;
  width: fit-content;
  font-size: 10px;
  padding: 0 10px;
  background-color: #091f36;
  border: #9e363a 1px solid;
  border-radius: 10px;
  color: #ffffff;
}

/* input type="date" */
  .input-box input:focus + label,
  .input-box input:valid + label {
    top: -8px;
    left: 10px;
    width: fit-content;
    font-size: 10px;
    padding: 0 10px;
    background-color: #091f36;
    border: #9e363a 1px solid;
    border-radius: 10px;
    color: #ffffff;
  }

  input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
  }


/* submit/next button */
.form-btn {
  padding: 8px;
  border-radius: 10px;
}
</style>
