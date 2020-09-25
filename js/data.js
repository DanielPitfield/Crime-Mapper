// TODO Migrate crime category information to database

const violence_sub_options = ["Murder", "Attempted Murder", "Manslaughter", "Conspiracy to murder", "Threats to kill", "Causing death or serious injury by dangerous driving", "Causing death by careless driving under the influence of drink or drugs", "Causing death by careless or inconsiderate driving", "Causing death or serious injury by driving (unlicensed driver)", "Causing death by aggrevated vehicle taking", "Corporate manslaughter", "Assualt (with intent to cause serious harm)", "Endangering life", "Harassment", "Racially or religiously aggravated harassment", "Racially or religiously aggravated assualt with injury", "Racially or religiously aggravated assualt without injury", "Assualt with injury", "Assualt without injury", "Assualt with injury on a constable", "Assualt without injury on a constable", "Stalking", "Maliciuos communications", "Cruelty to Children/Young Persons", "Child abduction", "Procuring illegal abortion", "Kidnapping", "Modern Slavery"];
const public_sub_options = ["Public fear, harm or distress", "Racially or religiously aggravated public fear, alarm or distress", "Violent disorder", "Other offences against the state or public order"];
const drug_sub_options = ["Trafficking in controlled drugs", "Posession of controlled drugs (Cannabis)", "Posession of controlled drugs (excluding Cannabis)", "Other drug offences"];
const vehicle_sub_options = ["Aggravated vehicle taking", "Theft from vehicle", "Theft or unauthorised taking of motor vehicle"];
const sexual_sub_options = ["Sexual Assualt", "Rape", "Causing sexual activity without consent", "Sexual activity with minor", "Sexual activity with a vulnerable person", "Sexual exploitation", "Abuse of a position of trust of a sexual nature", "Sexual grooming", "Exposure and voyeurism", "Unnatural sexual offences", "Other miscellaneous sexual offences"];
const arson_sub_options = ["Arson endangering life", "Arson not endangering life", "Criminal damage to a dwelling", "Criminal damage to a building other than a dwelling", "Criminal damage to a vehicle", "Other criminal damage"];
const weapons_sub_options = ["Possession of firearms with intent", "Possession of firearms offences", "Possession of other weapons", "Possession of article with blade or point", "Other firearms offences", "Other knives offences"];
const theft_sub_options = ["Blackmail", "Theft from the person", "Theft in a dwelling other than from an automatic machine or meter", "Theft by an employee", "Theft of mail", "Dishonest use of electricity", "Theft or unauthorised taking of a pedal cycle", "Shoplifting", "Theft from an automatic machine or meter", "Making off without payment", "Other theft"];
const burglary_sub_options = ["Burglary - Residential", "Attempted burglary - Residential", "Distraction burglary - Residential", "Attempted distraction burglary - Residential", "Aggravated burglary in a dwelling", "Burglary - Business and Community", "Attempted burglary - Business and Community", "Aggravated burglary - Business and Community"];
const robbery_sub_options = ["Robbery of business property", "Robbery of personal property"];
const misc_sub_options = ["Concealing an infant death close to birth", "Exploitation of prostitution", "Bigamy", "Soliciting for the purpose of prostitution", "Going equipped for stealing", "Making, supplying or possessing articles for use in fraud", "Profiting from or concealing knowledge of the proceeds of crime", "Handling stolen goods", "Threat or possession with intent to commit criminal damage", "Forgery or use of false drug prescription", "Fraud or forgery associated with vehicle or driver records", "Other forgery", "Possession of false documents", "Perjury", "Offender Management Act", "Aiding suicide", "Perverting the course of justice", "Absconding from lawful custody", "Bail offences", "Obscene publications", "Disclosure, obstruction, false or misleading statements", "Wildlife crime", "Dangerous driving", "Other notifiable offences"];
const other_sub_options = ["Unspecified Crime", "Other crime"];

const crimeTypeMappings = [
    { options: violence_sub_options, value: "Violence against the person", image_path: "violence.png" },
    { options: public_sub_options, value: "Public Order", image_path: "public_order.png" },
    { options: drug_sub_options, value: "Drug offences", image_path: "drugs.png" },
    { options: vehicle_sub_options, value: "Vehicle offences", image_path: "vehicle.png" },
    { options: sexual_sub_options, value: "Sexual offences", image_path: "sexual.png" },
    { options: arson_sub_options, value: "Arson and criminal damage", image_path: "arson.png" },
    { options: weapons_sub_options, value: "Possession of weapons", image_path: "weapons.png" },
    { options: theft_sub_options, value: "Theft", image_path: "theft.png" },
    { options: burglary_sub_options, value: "Burglary", image_path: "burglary.png" },
    { options: robbery_sub_options, value: "Robbery", image_path: "robbery.png" },
    { options: misc_sub_options, value: "Miscellaneous crimes against society", image_path: "other.png" },
    { options: other_sub_options, value: "Other", image_path: "other.png" }
];

const locMappings = [
    { text: "1/4", value: 0.25 },
    { text: "1/2", value: 0.5 },
    { text: "1", value: 1 },
    { text: "3", value: 3 },
    { text: "5", value: 5 },
    { text: "10", value: 10 },
    { text: "15", value: 15 },
    { text: "20", value: 20 },
    { text: "30", value: 30 },
    { text: "40", value: 40 },
    { text: "50", value: 50 },
    { text: "100", value: 100 },
    { text: "250", value: 250 }
];

const main_options = ["Violence against the person", "Public Order", "Drug offences", "Vehicle offences", "Sexual offences", "Arson and criminal damage", "Possession of weapons", "Theft", "Burglary", "Robbery", "Miscellaneous crimes against society", "Other"];
const all_option = ["[ALL]"];