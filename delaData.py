import pandas as pd
import os

def splitta_csv(filväg, rader_per_fil, output_mapp):
    os.makedirs(output_mapp, exist_ok=True)

    valda_kolumner = [
        "id", "title", "rank", "date", "artist",
        "url", "region", "chart", "trend", "streams"
    ]

    chunk_iterator = pd.read_csv(
        filväg,
        chunksize=rader_per_fil,
        low_memory=False,
        encoding='utf-8'
    )

    for idx, chunk in enumerate(chunk_iterator, start=1):
        # Byt namn på "Unnamed: 0" till "id", om den finns
        if "Unnamed: 0" in chunk.columns:
            chunk = chunk.rename(columns={"Unnamed: 0": "id"})

        # Plocka ut de 10 kolumnerna du vill ha
        chunk = chunk[valda_kolumner]

        filnamn = os.path.join(output_mapp, f"output_{idx}.csv")
        chunk.to_csv(filnamn, index=False, encoding='utf-8')
        print(f"Skapade: {filnamn}")

# Kör funktionen
splitta_csv(
    "C:/Users/alfre/OneDrive/Skrivbord/dataset.csv",
    100000,
    r"C:\Users\alfre\OneDrive\Skrivbord\UppDelatDataset"
)



