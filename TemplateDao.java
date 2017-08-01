@Repository
public interface TemplateDao {
    List<Template> getTemplateList(HashMap map);

    List<Template> getSearchResult(HashMap map);

    Template findTemplateByTemplateId(int templateId);

    void delete(int templateId);

    void deleteByPkIds(List<Integer> ids);

    void update(Template template);

    int insert(Template template);

    int getCount(Template template);

    int getTotal();
}