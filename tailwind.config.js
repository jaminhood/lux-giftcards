module.exports = {
 content: ["./**/*.php", "./src/**/*.js"],
 theme: {
  extend: {
   colors: {
    "main-primary": `#5E4A96`,
    "light-primary": `#EEEBF4`,
    "main-secondary": `#B0CB6D`,
   },
   fontFamily: {
    "sansation-bold": ["sansationBold"],
    "sansation-regular": ["sansationRegular"],
    "sansation-light": ["sansationLight"],
    "jost-regular": ["jostRegular"],
   },
  },
 },
 plugins: [require("@tailwindcss/typography")],
}
