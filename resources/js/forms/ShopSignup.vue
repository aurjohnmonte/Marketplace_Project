<template>
    <form method="POST" class="form-content">
        <div class="form-header">
            <h1>Shop up</h1>
            <p>Sign up as a <strong>{{ userType }}</strong></p>
        </div>

        <button class="form-btn" id="next-btn">Next</button>
    </form>
</template>

<script>
export default {
    data() {
        return {
            userType: 'seller'
        }
    }
}
</script>
