<template>
    <form method="POST" class="form-content">
        <div class="form-header">
            <h1>Sign up</h1>
            <p>Shop Details</p>
        </div>

        <div>
            <div v-for="details in shopInfo" :key="details.id" class="input-row">
                <div class="input-box">
                    <input
                        :type="details.type"
                        :name="details.name"
                        :id="details.id"
                    >
                    <label :for="details.name">{{ details.label }}</label>
                </div>

            </div>
        </div>
        <button class="form-btn style" id="next-btn">Next</button>
    </form>
</template>

<script>
export default {
    data() {
        return {
            shopInfo: [
                {label: 'Shop Name', type: 'text', name: 'shopName', id: 'shopName' },
                {label: 'Shop Category', type: 'text', name: 'shopCategory', id: 'shopCategory' },
                {label: 'Shop Description', type: 'text', name: 'shopDescription', id: 'shopDescription' },
                {label: 'Shop Address', type: 'text', name: 'shopAddress', id: 'shopAddress' },
                {label: 'Upload Shop Image', type: 'file', name: 'shopProfile', id: 'shopProfile' },
            ]
        }
    }
}
</script>
