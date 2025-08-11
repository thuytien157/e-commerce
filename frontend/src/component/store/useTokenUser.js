import { defineStore } from "pinia";
import { ref } from "vue";

export const useTokenUser = defineStore("tokenUser", () => {
  const tokenString = localStorage.getItem("token_huce");
  const userString = localStorage.getItem("user_huce");

  const token = ref(tokenString);
  const userId = ref(userString ? JSON.parse(userString).id : null);
  const username = ref(userString ? JSON.parse(userString).username : null);

  function setAuth(newToken, newUser) {
    token.value = newToken;
    username.value = newUser.username;
    userId.value = newUser.id;
    localStorage.setItem("token_huce", newToken);
    localStorage.setItem("user_huce", JSON.stringify(newUser));
    console.log('ssss' + newUser);
    
  }

  function clearAuth() {
    token.value = null;
    username.value = null;
    userId.value = null;
    localStorage.removeItem("token_huce");
    localStorage.removeItem("user_huce");
  }

  return { token, userId, username, setAuth, clearAuth };
});