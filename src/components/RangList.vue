<template>
  <div class="rang-list">
    <h2>Pirates Count by Country</h2>
    <ul>
      <li v-for="(country, index) in sortedUserCounts" :key="index">
        {{ index + 1 }}. {{ country.country }} ({{ country.user_count }})
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: "RangList",
  data() {
    return {
      userCounts: [],
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
  margin: 5px 0;
  padding: 5px;
  background-color: rgba(169, 169, 169, 0.9);
  border-radius: 8px;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
  color: #fff;
}
</style>
