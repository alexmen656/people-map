<template>
  <div class="rang-list">
    <h2>{{ $t("ranglist") }}</h2>
    <ul>
      <li v-for="(country, index) in sortedUserCounts" :key="index">
        {{ index + 1 }}. {{ country.country }} ({{ country.user_count }})
      </li>
    </ul>
    <h4>Language: <span @click="changeLanguage('en')" :class="isActiveLanguage('en')">EN</span> | <span @click="changeLanguage('de')" :class="isActiveLanguage('de')">DE</span></h4>
  </div>
</template>

<script>
export default {
  name: "RangList",
  data() {
    return {
      userCounts: [],
      currentLanguage: localStorage.getItem("language") || "en",
    };
  },
  computed: {
    sortedUserCounts() {
      return [...this.userCounts].sort((a, b) => b.user_count - a.user_count);
    },
  },
  mounted() {
    this.fetchUserCounts();
  },
  methods: {
    changeLanguage(lang) {
      this.$i18n.locale = lang;
      localStorage.setItem("language", lang);
      this.currentLanguage = lang; 
    },
    fetchUserCounts() {
      this.$axios
        .get("data.php")
        .then((response) => {
          this.userCounts = response.data;
        })
        .catch((error) => {
          console.error("Error fetching user counts:", error);
        });
    },
    isActiveLanguage(lang) {
      return this.currentLanguage === lang ? 'active-language' : '';
    },
  },
};
</script>

<style scoped>
.rang-list {
  position: absolute;
  top: 50%;
  right: 10px;
  transform: translateY(-50%);
  background-color: rgba(18, 18, 18, 0.6);
  padding: 15px;
  border: none;
  border-radius: 16px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  z-index: 1002;
}

.rang-list h2 {
  position: relative;
  margin: 0 0 10px;
  font-size: 1.2em;
  color: #fff;
}

.rang-list ul {
  position: relative;
  list-style: none;
  padding: 0;
  margin: 0;
}

.rang-list li {
  position: relative;
  margin: 10px 0;
  padding: 5px;
  background-color: rgba(169, 169, 169, 0.9);
  border-radius: 8px;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
  color: #fff;
}

.rang-list h4 {
  position: relative;
  margin: 5px 0 0 0;
  padding: 0;
  color: #fff;
  text-align: center;
  cursor: pointer;
}

.active-language {
  color: red;
}
</style>
