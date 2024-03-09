<template>
    <div class="container mt-2">
        <h3 class="text-center my-3">Contact</h3>
        <div v-show="requestResponse" :class="`alert alert-dismissible show fade alert-${requestResponse}`" role="alert">
            {{ requestResponse === 'success' ? 'Your message has been sent.' : 'An error occurred.' }}
            <button type="button" class="btn-close" @click="closeAlert" aria-label="Close"></button>
        </div>
        <form @submit.prevent="submitForm">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" v-model="email" class="form-control" id="email"
                    placeholder="name@example.com" @blur="validateEmail" :disabled="isSubmitting" required>
                <p v-if="emailError" style="color: red;">Invalid email.</p>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" name="message" v-model="message" id="message" rows="5"
                    @blur="validateMessage" :disabled="isSubmitting" required></textarea>
                <p v-if="messageError" style="color: red;">Input reqired.</p>
            </div>
            <div class="mb-3 text-center">
                <button type="submit" name="submit" :disabled="isSubmitting" class="btn btn-primary">Submit</button>
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
            requestResponse: '',
            emailError: false,
            messageError: false,
            isSubmitting: false,
        }
    },
    methods: {
        validateEmail() {
            const re = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            this.emailError = !re.test(this.email);
        },
        validateMessage() {
            this.messageError = this.message.trim().length === 0;
        },
        closeAlert() {
            this.requestResponse = '';
        },
        async submitForm(e) {
            this.validateEmail();
            this.validateMessage();
            if (!this.emailError && !this.messageError) {
                this.isSubmitting = true;
                const contactDetails = {
                    email: this.email,
                    message: this.message
                };
                try {
                    await axios.post('/contact', contactDetails);
                    this.requestResponse = 'success';
                    this.email = '';
                    this.message = '';
                } catch (error) {
                    this.requestResponse = 'warning';
                }
                this.isSubmitting = false;
            }
        }
    }
};
</script>