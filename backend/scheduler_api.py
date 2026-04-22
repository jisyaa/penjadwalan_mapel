from fastapi import FastAPI
from scheduler_ga import genetic_algorithm_improved, convert_full_output

app = FastAPI()

@app.get("/generate-jadwal")
def generate_jadwal():

    best_schedule, fitness_history = genetic_algorithm_improved()

    output = convert_full_output(best_schedule, fitness_history)

    return {
        "jadwal": output["schedule"],
        "fitness_best": max(fitness_history),
        "fitness_history": fitness_history,
        "generasi": len(fitness_history)
    }