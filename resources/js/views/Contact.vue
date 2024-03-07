<template>
    <div class="container mt-2">
        <h3 class="text-center my-5">Contact</h3>
        <form @submit.prevent="submitForm">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" v-model="email" class="form-control" id="email" placeholder="name@example.com" @blur="validateEmail" required>
                <p v-if="emailError" style="color: red;">Invalid email.</p>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" name="message" v-model="message"  id="message" rows="5" @blur="validateMessage" required></textarea>
                <p v-if="messageError" style="color: red;">Input reqired.</p>
            </div>
            <div class="mb-3 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    name: 'Contact',
    async created() {
        document.title = 'Contact - ' + document.querySelector('h1').textContent;
    },
    data() {
        return {
            email: '',
            message: '',
            emailError: false,
            messageError: false
        }
    },
    methods: {
        validateEmail() {
            const re =  /^[a-zA-Z0-9_+-]+(.[a-zA-Z0-9_+-]+)*@([a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9]*\.)+[a-zA-Z]{2,}$/;
            this.emailError = !re.test(this.email);
        },
        validateMessage() {
            this.messageError = this.message.trim().length === 0;
        },
        async submitForm() {
            this.validateEmail();
            this.validateMessage();
            if (!this.emailError && !this.messageError) {
                const contactDetails = {
                    email: this.email,
                    message: this.message
                };
                try {
                    const response = await axios.post('/contact', contactDetails);
                } catch (error) {
                    console.error(error);
                }
            }
        }
    }
};
</script>