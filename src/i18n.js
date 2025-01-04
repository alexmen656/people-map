import { createI18n } from "vue-i18n";

const messages = {
  en: {
    title: "Where Are You From? - Interactive Pirates Map",
    description: "Mark your location on the map and see where other pirates are from. Let's chart our global pirate community!",
    ranglist: "Pirates Count by Country"
  },
  de: {
    title: "Woher kommst du? - Interaktive Piratenkarte",
    description: "Markiere deinen Standort auf der Karte und entdecke, woher die anderen Piraten stammen. Lass uns die Weltkarte unserer Piratencrew zeichnen!",
    ranglist: "Anzahl der Piraten nach Land"
  },
};

const i18n = createI18n({
  legacy: false,
  locale: "en",
  messages,
});

export default i18n;
