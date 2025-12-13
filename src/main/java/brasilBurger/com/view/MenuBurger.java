package brasilBurger.com.view;

import brasilBurger.com.config.database.Database;
import brasilBurger.com.config.database.DatabaseImpl;
import brasilBurger.com.config.factory.repository.EntityName;
import brasilBurger.com.config.factory.repository.RepositoryFactory;
import brasilBurger.com.config.factory.service.ServiceFactory;
import brasilBurger.com.repositories.BurgerRepository;
import brasilBurger.com.repositories.Impl.BurgerRepositoryImpl;
import brasilBurger.com.services.BurgerService;
import brasilBurger.com.services.Impl.BurgerServiceImpl;

import java.util.Scanner;

public class MenuBurger {
    private static final Scanner sc = new Scanner(System.in);
    private final BurgerVue burgerVue;

    public MenuBurger() {
        this.burgerVue = new BurgerVue(); // BurgerVue récupère le service via la factory
    }

    public void menuBurgerGestion(){
        Vue.setSc(sc);

        while(true){
            switch (menuGestion()){
                case 1:
                    //burgerVue.creerBurger();
                    break;
                case 2:
                    //burgerVue.listerBurger();
                    break;
                case 3:
                    burgerVue.modifierBurger();
                    break;
                case 4:
                    //burgerVue.deleteBurger();
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
        System.out.println("1-Creer un burger");
        System.out.println("2-Lister un burger");
        System.out.println("3-Modifier un burger");
        System.out.println("4-Supprimer un burger");
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
