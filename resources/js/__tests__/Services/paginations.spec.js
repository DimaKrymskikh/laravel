import { getPaginationOptions } from "@/Services/pagination.js";

describe("@/Services/pagination.js", () => {
    it("getPaginationOptions задаёт разные объекты", () => {
        const pagination1 = getPaginationOptions();
        const pagination2 = getPaginationOptions();
        
        expect(pagination1).not.toBe(pagination2); 
        expect(pagination1.page).not.toBe(pagination2.page); 
        expect(pagination1.perPage).not.toBe(pagination2.perPage);
    });
});
