import { createI18n } from "vue-i18n";

const messages = {
  en: {
    title: "Where Are You From? - Interactive Pirates Map",
    description:
      "Mark your location on the map (click on a state). Zoom in to select your country more easily and see where other pirates are from. Let's chart our global pirate community! You can also add custom pins to highlight something special, such as your exact location, your home, or anything you like. Simply hold *Shift* and click anywhere on the map to place a pin. You can place as many pins as you want!",
    ranglist: "Pirates Count by Country",
  },
  de: {
    title: "Woher kommst du? - Interaktive Piratenkarte",
    description:
      "Markiere deinen Standort auf der Karte (klicke auf einen Staat). Zoome hinein, um dein Land leichter auszuwählen, und entdecke, woher die anderen Piraten stammen. Lass uns die weltweite Piraten-Community kartieren! Du kannst auch benutzerdefinierte Pins hinzufügen, um etwas Besonderes hervorzuheben, wie deinen genauen Standort, dein Zuhause oder alles, was dir gefällt. Halte einfach *Shift* gedrückt und klicke irgendwo auf der Karte, um einen Pin zu setzen. Du kannst so viele Pins setzen, wie du möchtest!",
    ranglist: "Piratenanzahl nach Land",
  },  
};

const i18n = createI18n({
  legacy: false,
  locale: "en",
  messages,
});

export default i18n;
