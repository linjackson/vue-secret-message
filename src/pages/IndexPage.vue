<template>
  <q-page class="flex flex-center text-center">
    <q-card class="q-pa-md shadow-2" bordered v-if="!loggedIn && !timedOut">
      <q-card-section class="text-center">
        <div class="text-grey-9 text-h5 text-weight-bold">Sign In</div>
        <div class="text-grey-8">Sign in for a secret message.</div>
      </q-card-section>
      <q-card-section>
        <q-form @submit="logIn">
          <q-card-section>
            <q-input
              dense
              outlined
              v-model="username"
              name="username"
              label="Username"
            ></q-input>
            <q-input
              dense
              outlined
              class="q-mt-md"
              v-model="password"
              type="password"
              name="password"
              label="Password"
            ></q-input>
          </q-card-section>
          <q-card-section>
            <q-btn
              style="border-radius: 8px"
              color="dark"
              rounded
              size="md"
              type="submit"
              label="Log In"
              no-caps
              class="full-width"
            ></q-btn>
          </q-card-section>
        </q-form>
      </q-card-section>
    </q-card>
    <q-card v-if="loggedIn">
      <q-card-section>
        <p>{{ message }}</p>
        <img
          src="/public/img/its-a-unix-system.gif"
          alt="It's a UNIX system, I know this!"
        />
        <audio autoplay>
          <source src="/public/audio/its-a-unix-system.mp3" type="audio/mpeg" />
        </audio>
      </q-card-section>
      <q-card-section>
        <q-btn
          style="border-radius: 8px"
          color="dark"
          rounded
          size="md"
          label="Log Out"
          no-caps
          class="full-width"
          @click="logOut"
        ></q-btn>
      </q-card-section>
    </q-card>
    <q-card v-if="timedOut">
      <q-card-section>
        <p>ACCESS DENIED...AND...YOU DIDN'T SAY THE MAGIC WORD!</p>
        <img
          src="/public/img/magic-word.gif"
          alt="You didn't say the magic word!"
        />
        <audio autoplay loop>
          <source
            src="/public/audio/didnt-say-the-magic-word.mp3"
            type="audio/mpeg"
          />
        </audio>
        <p>Try again in {{ countdown }}.</p>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { useQuasar } from "quasar";
import { onMounted, ref } from "vue";
import { api } from "src/boot/axios";

// Set up variables
const $q = useQuasar();
const username = ref();
const password = ref();
let token;
let message = ref();
let loggedIn = ref();
let failedLogin = 0;
const timedOut = ref();
const countdown = ref(30);
var countdownInterval = null;

// Get a session token from the server and save it locally
async function getToken() {
  return api({
    method: "post",
    url: "sessionstart.php",
  })
    .then((response) => {
      token = response.data.token;
      failedLogin = response.data.login_attempts;
      if (response.data.countdown) {
        timedOut.value = true;
        countdown.value = response.data.countdown;
        if (!countdownInterval) {
          countdownInterval = setInterval(countdownTimer, 1000);
        }
      }
      return token;
    })
    .catch(() => {
      $q.notify({
        color: "negative",
        position: "top",
        message: "Loading failed",
        icon: "report_problem",
      });
    });
}

// Run the countdown timer
function countdownTimer() {
  if (countdown.value >= 1) {
    countdown.value--;
  } else {
    timedOut.value = false;
    failedLogin = 0;
    clearInterval(countdownInterval);
    countdownInterval = null;
  }
}

// Handle login
async function logIn() {
  const bodyFormData = new FormData();
  const loginusername = username.value;
  const loginpassword = password.value;

  bodyFormData.append("username", loginusername);
  bodyFormData.append("password", loginpassword);
  bodyFormData.append("token", token);

  return api({
    method: "post",
    url: "login.php",
    data: bodyFormData,
    headers: { "Content-Type": "multipart/form-data" },
  })
    .then((response) => {
      // Successful login
      message.value = response.data.message;
      loggedIn.value = true;
      return message;
    })
    .catch((error) => {
      // If there was an error
      if (error.response) {
        // If the server returned an error in the response
        if (error.response.data.countdown) {
          // If login is timed out, set the timeout state and start the countdown timer
          timedOut.value = true;
          countdown.value = error.response.data.countdown;
          if (!countdownInterval) {
            countdownInterval = setInterval(countdownTimer, 1000);
          }
        }
        // Show a notification
        $q.notify({
          color: "negative",
          position: "top",
          message: "ACCESS DENIED",
          icon: "report_problem",
        });
      } else if (error.request) {
        // If there was an error in the request, show a notification
        $q.notify({
          color: "negative",
          position: "top",
          message: "ACCESS DENIED",
          icon: "report_problem",
        });
      }
    });
}

// Handle logout
async function logOut() {
  const logoutResponse = async () => {
    return api({
      method: "post",
      url: "logout.php",
    })
      .then((response) => {
        // Successful logout
        const message = response.data.message;
        loggedIn.value = false;
        return message;
      })
      .catch(() => {
        // If logout fails, show a notification
        $q.notify({
          color: "negative",
          position: "top",
          message: "Logout failed",
          icon: "report_problem",
        });
      });
  };

  // Get the logout response
  const response = await logoutResponse();
  // Get a new session token
  token = await getToken();
  return response;
}

defineOptions({
  name: "LoginPage",
});

// On load, get the initial session token
onMounted(async () => {
  token = await getToken();
});
</script>
