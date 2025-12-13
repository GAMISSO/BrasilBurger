package brasilBurger.com.view;

import java.util.Scanner;

public class MenuPrincipal {
    private static final Scanner sc = new Scanner(System.in);
    public static void menuPrincipalGestion(){
        Vue.setSc(sc);
        while(true){
            switch (menuGestion()){
                case 1:
                    MenuBurger menuBurger = new MenuBurger();
                    menuBurger.menuBurgerGestion();
                    break;
                case 2:
                    MenuComplement.menuComplementGestion();
                    break;
                case 3:
                    MenuMenu.menuMenuGestion();
                    break;
                case 4:
                    System.exit(0);
                    break;
                default:
                    System.out.println("veuillez faire un choix entre 1 et 3");
                    break;
            }
        }
    }

    public static int menuGestion(){
        System.out.println("1-Gerer un burger");
        System.out.println("2-Gerer un complement");
        System.out.println("3-Gerer un menu");
        System.out.println("4-Quitter");
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
