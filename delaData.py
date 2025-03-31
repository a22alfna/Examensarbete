import pandas as pd
import os

def splitta_csv(filväg, rader_per_fil, output_mapp):
    # Skapa mappen om den inte redan finns
    os.makedirs(output_mapp, exist_ok=True)

    # Skapa en iterator för att läsa filen i bitar (chunks)
    chunk_iterator = pd.read_csv(
        filväg,
        chunksize=rader_per_fil,
        usecols=range(10),
        low_memory=False
    )

    # Gå igenom varje chunk och spara som egen fil
    for idx, chunk in enumerate(chunk_iterator, start=1):
        filnamn = os.path.join(output_mapp, f"output_{idx}.csv")
        chunk.to_csv(filnamn, index=False)
        print(f"Skapade fil: {filnamn}")

# Användning
splitta_csv(
    "C:/Users/alfre/OneDrive/Skrivbord/dataset.csv",       # Filväg till stora CSV-filen
    100000,                                                 # Rader per fil
    r"C:\Users\alfre\OneDrive\Skrivbord\UppDelatDataset"   # Mapp där delade filer sparas
)


