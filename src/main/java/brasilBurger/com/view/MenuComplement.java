package brasilBurger.com.view;

import java.util.Scanner;

public class MenuComplement {

    private static final Scanner sc = new Scanner(System.in);
    private final ComplementVue complementVue;

    public MenuComplement() {
        this.complementVue = new ComplementVue(); // BurgerVue récupère le service via la factory
    }
    public void menuComplementGestion(){
        Vue.setSc(sc);
        ComplementVue complementVue = new ComplementVue();

        while(true){
            switch (menuGestion()){
                case 1:
                    //complementVue.creerComplement();
                    break;
                case 2:
                    //complementVue.listerComplement();
                    break;
                case 3:
                    //complementVue.modifierComplement();
                    break;
                case 4:
                    complementVue.supprimerComplement();
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
        System.out.println("1-Creer un complement");
        System.out.println("2-Lister un complement");
        System.out.println("3-Modifier un complement");
        System.out.println("4-Supprimer un complement");
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
