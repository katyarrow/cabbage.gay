class POW {

    answers = [];
    finishedCount = 0;
    currentTotalTries = 0;
    estimatedTotalTries = 1;
    captcha = null;

    isFinished() {
        return this.finishedCount == this.answers.length;
    }

    hasAllAnswers() {
        return this.answers.filter(a => a !== null);
    }

    async calculatePowAnswers(captcha) {
        this.captcha = captcha;
        this.characters = captcha.characters;
        let puzzles = captcha.puzzles;
        let difficulty = captcha.difficulty;
        this.answers = puzzles.map(p => null);
        this.estimatedTotalTries = puzzles.length * Math.pow(10, difficulty) * 0.75;
        puzzles.forEach(async (puzzle, index) => {
            let salt = puzzle[0];
            let hash = puzzle[1];
            let tryIndex = -1;
            let maxTries = Math.pow(10, difficulty) + 1;
            while(tryIndex < maxTries) {
                this.currentTotalTries++;
                tryIndex++;
                if(await this.verifyHash(hash, salt + tryIndex)) {
                    this.tried = [];
                    this.answers[index] = tryIndex;
                    break;
                }
            }
            this.finishedCount++;
        });
    }

    async verifyHash(hash, message) {
        const msgUint8 = new TextEncoder().encode(message);
        const hashBuffer = await window.crypto.subtle.digest("SHA-256", msgUint8);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        const hashHex = hashArray
          .map((b) => b.toString(16).padStart(2, "0"))
          .join("");
        return hash == hashHex;
    }
}

window.POW = POW;