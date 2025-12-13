package brasilBurger.com.view;


import brasilBurger.com.config.factory.repository.EntityName;
import brasilBurger.com.config.factory.service.ServiceFactory;
import brasilBurger.com.entities.Burger;
import brasilBurger.com.entities.Complement;
import brasilBurger.com.entities.Enum.TypeComplement;
import brasilBurger.com.services.BurgerService;
import brasilBurger.com.services.ComplementService;

import java.time.LocalDate;
import java.util.List;

public class ComplementVue extends Vue {
    private final ComplementService complementService =
            ServiceFactory.getInstance(EntityName.Complement, ComplementService.class);
    /*
    public void creerComplement() {
        Complement complement = new Complement();
        complement.setNom(saisieChampOblig("Nom complement:"));
        complement.setPrix(saisieEntierOblig("Prix complement:"));
        System.out.print("1-Frite");
        System.out.print("2-Boisson");
        int choice=sc.nextInt();
        if(choice==1){
            complement.setTypeComplement(TypeComplement.Frite);
        }if(choice==2){
            complement.setTypeComplement(TypeComplement.Boisson);
        }
        complement.setCreatedAt(LocalDate.now());
        complement.setImage(saisieChampOblig("Entrer l'url de limage: "));
        complementService.createComplement(complement);
    }*/


    public void listerComplement(){
        List<Complement> complements=complementService.getAllComplements();
        for(Complement complement:complements){
            System.out.print("Id: "+complement.getId());
            System.out.print("Nom: "+complement.getNom());
            System.out.print("Prix: "+complement.getPrix());
            System.out.print("Type de complement: "+complement.getTypeComplement());
            System.out.print("Date: "+complement.getCreatedAt());
            System.out.println("============================================================");
        }

    }

    /*
    public void modifierComplement(){
        List<Complement> complemnts=complementService.getAllComplements();
        System.out.print("Veuillez entrez le numéro du burger qui vous voulez modifier ?");
        int choice=sc.nextInt();
        for (Complement complement:complemnts){
            if(complement.getId()==choice){
                while(true) {
                    System.out.println("Vous voulez modifier quoi ?!");
                    System.out.println("1-nom");
                    System.out.println("2-Prix");
                    System.out.println("3-Image");
                    System.out.print("Veuillez entrez le numéro de ce que vous vouliez modifier ?");
                    int choice_two = sc.nextInt();
                    if (choice_two == 1) {
                        System.out.println("Entrez le nouveau nom : ");
                        String nom = sc.next();
                        complement.setNom(nom);
                        break;
                    }
                    if (choice_two == 2) {
                        System.out.println("Entrez le nouveau prix : ");
                        int prix = sc.nextInt();
                        complement.setPrix(prix);
                        break;
                    }
                    if (choice_two == 3) {
                        System.out.println("Entrez le nouveau image_url : ");
                        String image = sc.next();
                        complement.setImage(image);
                        break;
                    }
                }
                complementService.updateComplement(complement);
            }
        }

    }*/
}
