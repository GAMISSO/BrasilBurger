package brasilBurger.com.view;

import java.util.Scanner;

public class MenuMenu {

    private static final Scanner sc = new Scanner(System.in);

    public static void menuMenuGestion(){
        Vue.setSc(sc);
        MenuVue menuVue = new MenuVue();
        while(true){
            switch (menuGestion()){
                case 1:
                    //menuVue.creerMenu();
                    break;
                case 2:
                    menuVue.listerMenu();
                    break;
                case 3:
                    //menuVue.modifierMenu();
                    break;
                case 4:
                    //menuVue.supprimerMenu();
                    break;
                case 5:
                    System.exit(0);
                    break;
                default:
                    System.out.println("veuillez faire un choix entre 1 et 5");
                    break;
            }
        }
    }

    public static int menuGestion(){
        System.out.println("1-Creer un Menu");
        System.out.println("2-Lister un Menu");
        System.out.println("3-Modifier un Menu");
        System.out.println("4-Supprimer un Menu");
        System.out.println("5-Quitter");
        int choice;
        while(true){
            try{
                choice=sc.nextInt();
                break;
            }catch (Exception e){
                System.out.println("Veuillez choisir une choix");
                sc.next();
            }
        }
        sc.nextLine();
        return choice;
    }

}
